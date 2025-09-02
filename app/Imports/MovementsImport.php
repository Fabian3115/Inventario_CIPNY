<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Movement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class MovementsImport implements ToCollection, WithHeadingRow
{
    protected bool $dryRun;
    protected int $total = 0;
    protected int $inserted = 0;
    protected int $failed = 0;
    protected array $errors = [];

    public function __construct(bool $dryRun = false)
    {
        $this->dryRun = $dryRun;
    }

    public function collection(Collection $rows)
    {
        // Normaliza encabezados esperados
        // product_code, date_products, type, amount, delivered_to, area, taken_by

        foreach ($rows as $index => $row) {
            $this->total++;

            // Número de fila "real" (sumando 2 por encabezados)
            $excelRow = $index + 2;

            // Arreglo base
            $payload = [
                'product_code'  => trim((string)($row['product_code'] ?? '')),
                'requisition' => isset($row['requisition']) ? trim((string)$row['requisition']) : null,
                'date_products' => $row['date_products'] ?? null,
                'type'          => strtolower(trim((string)($row['type'] ?? ''))),
                'amount'        => $row['amount'] ?? null,
                'delivered_to'  => isset($row['delivered_to']) ? trim((string)$row['delivered_to']) : null,
                'area'          => isset($row['area']) ? trim((string)$row['area']) : null,
                'taken_by'      => isset($row['taken_by']) ? trim((string)$row['taken_by']) : null,

            ];

            // Validación simple de campos base
            $v = Validator::make($payload, [
                'product_code'  => ['required', 'integer', 'exists:products,code'],
                'requisition' => ['nullable','string','max:255'],
                'date_products' => ['required'],
                'type'          => ['required', 'in:entrada,salida'],
                'amount'        => ['required', 'integer', 'min:1'],
                'delivered_to'  => ['nullable', 'string', 'max:255'],
                'area'          => ['nullable', 'string', 'max:255'],
                'taken_by'      => ['nullable', 'string', 'max:255'],
            ], [
                'product_code.exists' => 'El código de producto no existe.',
            ]);

            if ($v->fails()) {
                $this->fail($excelRow, $v->errors()->first());
                continue;
            }

            // Parse de fecha (acepta string o serial de Excel)
            $date = $this->parseDate($payload['date_products']);
            if (!$date) {
                $this->fail($excelRow, 'Fecha inválida en date_products.');
                continue;
            }

            // Obtener producto por código
            $product = Product::where('code', (int)$payload['product_code'])->first();
            if (!$product) {
                $this->fail($excelRow, 'Producto no encontrado por product_code.');
                continue;
            }

            $amount = (int) $payload['amount'];
            $type   = $payload['type'];

            // Reglas extra para salida (campos condicionales)
            if ($type === 'salida') {
                if (empty($payload['delivered_to']) || empty($payload['area']) || empty($payload['taken_by'])) {
                    $this->fail($excelRow, 'Para SALIDA se requieren delivered_to, area y taken_by.');
                    continue;
                }
            }

            // Validación de stock para salida
            if ($type === 'salida' && $amount > $product->stock) {
                $this->fail($excelRow, 'Stock insuficiente. Disponible: ' . $product->stock);
                continue;
            }

            // Si es simulación, no guardar, solo contar como "insertable"
            if ($this->dryRun) {
                $this->inserted++; // cuenta como válido/importable
                // (Opcional) Simular ajuste: $product->stock += o -= $amount
                // pero no lo persistimos.
                continue;
            }

            // Persistir dentro de transacción con lock
            try {
                DB::transaction(function () use ($product, $type, $amount, $date, $payload) {
                    // Lock del producto
                    $p = Product::lockForUpdate()->find($product->id);
                    if ($type === 'salida') {
                        if ($amount > $p->stock) {
                            throw new \RuntimeException('Stock insuficiente durante la transacción.');
                        }
                        $p->decrement('stock', $amount);
                    } else {
                        $p->increment('stock', $amount);
                    }

                    Movement::create([
                        'product_id'    => $p->id,
                        'requisition'   => $payload['requisition'], // <-- nuevo
                        'date_products' => $date->format('Y-m-d'),
                        'type'          => $type,
                        'amount'        => $amount,
                        'delivered_to'  => $payload['delivered_to'],
                        'area'          => $payload['area'],
                        'taken_by'      => $payload['taken_by'],
                    ]);
                });

                $this->inserted++;
            } catch (\Throwable $e) {
                $this->fail($excelRow, 'Error al guardar: ' . $e->getMessage());
            }
        }
    }

    protected function parseDate($value): ?Carbon
    {
        // Si viene como número (serial Excel)
        if (is_numeric($value)) {
            try {
                // Excel usa 1900 base; PhpSpreadsheet helper:
                $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
                return Carbon::createFromTimestamp($timestamp)->startOfDay();
            } catch (\Throwable $e) {
                return null;
            }
        }

        // Si viene como texto
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function fail(int $row, string $message): void
    {
        $this->failed++;
        $this->errors[] = ['row' => $row, 'message' => $message];
    }

    public function getSummary(): array
    {
        return [
            'dry_run'  => $this->dryRun,
            'total'    => $this->total,
            'inserted' => $this->inserted,
            'failed'   => $this->failed,
            'errors'   => $this->errors,
        ];
    }
}
