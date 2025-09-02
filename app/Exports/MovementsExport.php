<?php

namespace App\Exports;

use App\Models\Movement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class MovementsExport implements WithMultipleSheets
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            // Hoja principal con los datos
            new MovementsDataSheet($this->filters),
            // Hoja “Resumen” de filtros aplicados
            new MovementsSummarySheet($this->filters),
        ];
    }
}

/**
 * Hoja de datos con filtros, freeze, formatos y estilos.
 */
class MovementsDataSheet implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithColumnFormatting, WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Movement::query()
            ->with('product')
            ->when(!empty($this->filters['type']), fn($q) => $q->where('type', $this->filters['type']))
            ->when(!empty($this->filters['product_id']), fn($q) => $q->where('product_id', $this->filters['product_id']))
            ->when(!empty($this->filters['area']), fn($q) => $q->where('area', 'like', '%' . $this->filters['area'] . '%'))
            ->when(!empty($this->filters['date_from']), fn($q) => $q->whereDate('date_products', '>=', $this->filters['date_from']))
            ->when(!empty($this->filters['date_to']), fn($q) => $q->whereDate('date_products', '<=', $this->filters['date_to']))
            ->orderByDesc('date_products')
            ->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Código Producto',
            'Producto',
            'Requisición',               // <-- nuevo
            'Tipo',
            'Cantidad',
            'Fecha Movimiento',
            'Entregado a',
            'Área',
            'Sacado por',
            'Stock Actual (al exportar)',
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->id,
            optional($movement->product)->code,
            optional($movement->product)->description,
            $movement->requisition,                         // <-- nuevo
            ucfirst($movement->type),
            (int) $movement->amount,
            optional($movement->date_products)?->format('Y-m-d'),
            $movement->delivered_to,
            $movement->area,
            $movement->taken_by,
            optional($movement->product)->stock,
        ];
    }

    // FORMATO DE COLUMNAS (columna F como fecha)
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD2, // Fecha Movimiento
        ];
    }

    // ESTILOS (encabezado)
    public function styles(Worksheet $sheet)
    {
        // Negrita y un fondo suave en encabezados
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9EFFB'],
            ],
            'borders' => [
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ]);

        return [];
    }

    // EVENTOS (AutoFilter, Freeze Pane, Ancho, Filtros por tabla)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Congelar encabezado
                $sheet->freezePane('A2');

                // AutoFilter (activa los filtros en la fila 1 para todas las columnas con datos)
                // Detecta la última fila con datos:
                $highestRow = $sheet->getHighestRow();
                // y la última columna con datos (debería ser J):
                $highestColumn = $sheet->getHighestColumn();
                // Aplica AutoFilter al rango completo de la tabla:
                $sheet->setAutoFilter("A1:{$highestColumn}{$highestRow}");

                // Opcional: ancho mínimo de columnas para legibilidad (si no usas ShouldAutoSize)
                // $event->sheet->getColumnDimension('C')->setWidth(50);

                // Opcional: formato condicional básico sobre "Tipo"
                // Verde para Entrada, Rojo para Salida:
                $typeCol = 'D';
                $conditionalStyles = $sheet->getStyle("{$typeCol}2:{$typeCol}{$highestRow}")->getConditionalStyles();

                $green = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                $green->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
                $green->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
                $green->setText('Entrada');
                $green->getStyle()->getFont()->getColor()->setARGB('FF2E7D32'); // verde
                $green->getStyle()->getFont()->setBold(true);

                $red = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                $red->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
                $red->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
                $red->setText('Salida');
                $red->getStyle()->getFont()->getColor()->setARGB('FFC62828'); // rojo
                $red->getStyle()->getFont()->setBold(true);

                $conditionalStyles[] = $green;
                $conditionalStyles[] = $red;
                $sheet->getStyle("{$typeCol}2:{$typeCol}{$highestRow}")->setConditionalStyles($conditionalStyles);
            }
        ];
    }
}

/**
 * Hoja “Resumen” con los filtros aplicados.
 */
class MovementsSummarySheet implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    // Reutilizamos la misma query para contar resultados; mapeamos solo una fila de resumen.
    public function query()
    {
        // Solo para que Maatwebsite inicialice la sheet; no necesitamos datos reales.
        // Devolvemos un query vacío y usamos headings + mapping con una sola fila.
        return Movement::query()->limit(0);
    }

    public function headings(): array
    {
        return ['Filtro', 'Valor'];
    }

    public function map($row): array
    {
        // No se usa; vamos a sobreescribir esta hoja en AfterSheet con datos fijos
        return [];
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $pairs = [
                    ['Generado el', now()->format('Y-m-d H:i:s')],
                    ['Tipo', $this->filters['type'] ?? '—'],
                    ['Producto ID', $this->filters['product_id'] ?? '—'],
                    ['Área', $this->filters['area'] ?? '—'],
                    ['Desde', $this->filters['date_from'] ?? '—'],
                    ['Hasta', $this->filters['date_to'] ?? '—'],
                ];

                // Escribimos encabezados
                $sheet->setCellValue('A1', 'Filtro');
                $sheet->setCellValue('B1', 'Valor');
                $sheet->getStyle('A1:B1')->getFont()->setBold(true);

                // Escribimos pares
                $r = 2;
                foreach ($pairs as $p) {
                    $sheet->setCellValue("A{$r}", $p[0]);
                    $sheet->setCellValue("B{$r}", $p[1]);
                    $r++;
                }

                // Estética leve
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(40);
            }
        ];
    }
}
