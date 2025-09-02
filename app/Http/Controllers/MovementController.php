<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MovementsExport;
use App\Exports\MovementsTemplateExport;
use App\Imports\MovementsImport;
use Maatwebsite\Excel\Facades\Excel;

class MovementController extends Controller
{
    /**
     * Listado con filtros + paginación
     */
    public function index(Request $request)
    {
        $products = Product::orderBy('description')->get(['id', 'code', 'description']);

        $query = Movement::with('product')
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('product_id'), fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->filled('area'), fn($q) => $q->where('area', 'like', '%' . $request->area . '%'))
            ->when($request->filled('requisition'), fn($q) => $q->where('requisition', 'like', '%' . $request->requisition . '%')) // <-- nuevo
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('date_products', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('date_products', '<=', $request->date_to))
            ->orderByDesc('date_products')
            ->orderByDesc('id');

        $movements = $query->paginate(10)->appends($request->query());

        return view('movimientos.index', compact('movements', 'products'));
    }

    /**
     * Formulario de creación (vista interactiva)
     */
    public function create()
    {
        // Puedes cargar solo lo necesario para el combo
        $products = Product::orderBy('description')->get([
            'id',
            'code',
            'description',
            'stock',
            'categories',
            'extent',
            'warehouse'
        ]);

        return view('movimientos.create', compact('products'));
    }

    /**
     * Guardar movimiento con validación de stock y actualización de producto
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'date_products' => ['required', 'date'],
            'type'          => ['required', 'in:entrada,salida'],
            'amount'        => ['required', 'integer', 'min:1'],
            'delivered_to'  => ['nullable', 'string', 'max:255'],
            'area'          => ['nullable', 'string', 'max:255'],
            'taken_by'      => ['nullable', 'string', 'max:255'],
            'requisition'   => ['nullable', 'string', 'max:255'], // <-- nuevo
        ]);

        if ($data['type'] === 'salida') {
            $request->validate([
                'delivered_to' => ['required', 'string', 'max:255'],
                'area'         => ['required', 'string', 'max:255'],
                'taken_by'     => ['required', 'string', 'max:255'],
            ]);
        }

        $product = Product::lockForUpdate()->findOrFail($data['product_id']);

        return DB::transaction(function () use ($data, $product) {
            if ($data['type'] === 'salida' && $data['amount'] > $product->stock) {
                return back()->withInput()->withErrors([
                    'amount' => 'No puedes retirar más de lo disponible en stock (' . $product->stock . ').'
                ]);
            }

            $movement = Movement::create($data);

            if ($data['type'] === 'entrada') {
                $product->increment('stock', (int)$data['amount']);
            } else {
                $product->decrement('stock', (int)$data['amount']);
            }

            return redirect()->route('movements.index')->with('success', 'Movimiento registrado correctamente.');
        });
    }

    public function edit(Movement $movement)
    {
        // Productos para el select
        $products = Product::orderBy('description')->get([
            'id',
            'code',
            'description',
            'stock',
            'categories',
            'extent',
            'warehouse'
        ]);

        // Catálogos opcionales (si no los pasas desde el controlador principal)
        $areas = ['Bodega A', 'Bodega B', 'Mantenimiento', 'Producción', 'Sistemas', 'Administración'];
        $takenByList = ['Juan Pérez', 'Ana Gómez', 'Carlos Ruiz', 'María López', 'Invitado'];

        return view('movimientos.edit', compact('movement', 'products', 'areas', 'takenByList'));
    }

    public function update(Request $request, Movement $movement)
    {
        // Validación base
        $data = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'date_products' => ['required', 'date'],
            'type'          => ['required', 'in:entrada,salida'],
            'amount'        => ['required', 'integer', 'min:1'],
            'delivered_to'  => ['nullable', 'string', 'max:255'],
            'area'          => ['nullable', 'string', 'max:255'],
            'taken_by'      => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['type'] === 'salida') {
            $request->validate([
                'delivered_to' => ['required', 'string', 'max:255'],
                'area'         => ['required', 'string', 'max:255'],
                'taken_by'     => ['required', 'string', 'max:255'],
            ]);
        }

        // Transacción con locks para evitar condiciones de carrera
        return DB::transaction(function () use ($movement, $data) {
            $oldProductId = $movement->product_id;
            $oldType      = $movement->type;
            $oldAmount    = (int) $movement->amount;

            $newProductId = (int) $data['product_id'];
            $newType      = $data['type'];
            $newAmount    = (int) $data['amount'];

            // Lock de productos implicados
            $oldProduct = Product::lockForUpdate()->find($oldProductId);
            $newProduct = Product::lockForUpdate()->find($newProductId);

            if (!$newProduct) {
                return back()->withInput()->withErrors(['product_id' => 'Producto inválido.']);
            }

            // 1) Revertir el efecto del movimiento anterior sobre el producto anterior
            if ($oldProduct) {
                if ($oldType === 'entrada') {
                    // Se había SUMADO stock, ahora restamos
                    if ($oldProduct->stock < $oldAmount) {
                        return back()->withInput()->withErrors([
                            'amount' => 'No se puede revertir la entrada previa (stock insuficiente en el producto original).'
                        ]);
                    }
                    $oldProduct->decrement('stock', $oldAmount);
                } else {
                    // Se había RESTADO stock, ahora sumamos
                    $oldProduct->increment('stock', $oldAmount);
                }
            }

            // 2) Validar y aplicar el nuevo efecto al nuevo producto
            if ($newType === 'salida' && $newAmount > $newProduct->stock) {
                // Revertimos la reversión para no dejar inconsistencias si el mismo producto es el viejo
                // (Caso distinto de producto ya quedó revertido correctamente arriba, no hay nada más que revertir)
                // Como estamos en transacción, bastaría con devolver error y el rollback deja todo como estaba.
                return back()->withInput()->withErrors([
                    'amount' => 'Stock insuficiente en el producto seleccionado. Disponible: ' . $newProduct->stock
                ]);
            }

            if ($newType === 'entrada') {
                $newProduct->increment('stock', $newAmount);
            } else {
                $newProduct->decrement('stock', $newAmount);
            }

            // 3) Actualizar el movimiento
            $movement->update($data);

            return redirect()
                ->route('movements.index')
                ->with('success', 'Movimiento actualizado correctamente.');
        });
    }


    /**
     * Exportar a Excel respetando los filtros actuales
     */
    public function export(Request $request)
    {
        $filters = $request->only(['type', 'product_id', 'area', 'date_from', 'date_to']);
        $filename = 'movimientos_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new MovementsExport($filters), $filename);
    }

    /**
     * Historial (JSON) de los últimos 5 movimientos de un producto
     * Usado por la vista de crear para mostrar historial lateral
     */
    public function history(Product $product)
    {
        $items = Movement::where('product_id', $product->id)
            ->orderByDesc('date_products')
            ->orderByDesc('id')
            ->take(5)
            ->get(['id', 'type', 'amount', 'date_products', 'delivered_to', 'area', 'taken_by']);

        return response()->json([
            'product_id' => $product->id,
            'history' => $items->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => $m->type,
                    'amount' => (int)$m->amount,
                    'date' => optional($m->date_products)->format('Y-m-d'),
                    'delivered_to' => $m->delivered_to,
                    'area' => $m->area,
                    'taken_by' => $m->taken_by,
                ];
            }),
        ]);
    }

    /**
     * (Opcional) Eliminar un movimiento revirtiendo el stock
     */
    public function destroy(Movement $movement)
    {
        return DB::transaction(function () use ($movement) {
            $product = Product::lockForUpdate()->find($movement->product_id);

            if ($product) {
                // Revertir stock según el tipo del movimiento
                if ($movement->type === 'entrada') {
                    // Si borras una entrada, disminuye el stock
                    $product->decrement('stock', (int)$movement->amount);
                } else {
                    // Si borras una salida, aumenta el stock
                    $product->increment('stock', (int)$movement->amount);
                }
            }

            $movement->delete();

            return redirect()
                ->back()
                ->with('success', 'Movimiento eliminado y stock revertido.');
        });
    }

    public function importForm()
    {
        return view('movimientos.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'], // 10MB
            'dry_run' => ['nullable', 'boolean']
        ]);

        $dryRun = (bool) $request->boolean('dry_run');

        // Ejecutar import y obtener resumen
        $import = new MovementsImport($dryRun);
        Excel::import($import, $request->file('file'));

        $summary = $import->getSummary();

        return redirect()
            ->route('movements.import.form')
            ->with('import_summary', $summary);
    }

    public function template()
    {
        $filename = 'plantilla_movimientos.xlsx';
        return Excel::download(new MovementsTemplateExport, $filename);
    }
}
