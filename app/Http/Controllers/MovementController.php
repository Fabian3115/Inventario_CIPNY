<?php

namespace App\Http\Controllers;

use App\Exports\MovementsInExport;
use App\Exports\MovementsOutExport;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class MovementController extends Controller
{

    /**
     * ENTRADA
     */


    public function index_entrada(Request $r)
    {
        // Base: ENTRADAS con alias
        $base = Movement::query()
            ->from('movements as m')
            ->join('products as p', 'm.product_id', '=', 'p.id')
            ->where('m.type', 'entrada')
            ->select(
                'm.*',
                'p.code as p_code',
                'p.description as p_description',
                'p.extent as p_extent',
                'p.warehouse as p_warehouse'
            );

        // Filtros
        if ($r->filled('q')) {
            $kw = trim($r->q);
            $base->where(function ($q) use ($kw) {
                $q->where('p.description', 'like', "%{$kw}%")
                    ->orWhere('p.code', 'like', "%{$kw}%");
            });
        }
        if ($r->filled('product_id'))   $base->where('m.product_id', $r->product_id);
        if ($r->filled('delivered_to')) $base->where('m.delivered_to', 'like', '%' . $r->delivered_to . '%');
        if ($r->filled('taken_by'))     $base->where('m.taken_by', 'like', '%' . $r->taken_by . '%');
        if ($r->filled('date_from'))    $base->whereDate('m.date_products', '>=', $r->date_from);
        if ($r->filled('date_to'))      $base->whereDate('m.date_products', '<=', $r->date_to);

        // Orden
        $allowed = ['date_products', 'amount', 'p_code', 'p_description', 'delivered_to', 'taken_by'];
        $sort = in_array($r->get('sort'), $allowed) ? $r->get('sort') : 'date_products';
        $dir  = in_array($r->get('dir'), ['asc', 'desc']) ? $r->get('dir') : 'desc';

        // Mapear columnas para evitar ambigüedad
        $sortMap = [
            'p_code'        => 'p_code',
            'p_description' => 'p_description',
            'date_products' => 'm.date_products',
            'amount'        => 'm.amount',
            'delivered_to'  => 'm.delivered_to',
            'taken_by'      => 'm.taken_by',
        ];
        $base->orderBy($sortMap[$sort] ?? 'm.date_products', $dir);

        // KPIs antes de paginar
        $k = clone $base;
        $stats = [
            'count'      => (clone $k)->count(),
            'sum_amount' => (clone $k)->sum('m.amount'),
            'today'      => (clone $k)->whereDate('m.date_products', now()->toDateString())->count(),
        ];

        // Datos
        $entries = $base->paginate(12)->withQueryString();

        // Combos
        $products   = Product::orderBy('description')->get(['id', 'code', 'description']);
        $deliverers = Movement::where('type', 'entrada')
            ->whereNotNull('delivered_to')
            ->distinct()->orderBy('delivered_to')
            ->pluck('delivered_to');

        return view('movimientos.lista_entrada', compact('entries', 'products', 'deliverers', 'stats'));
    }

    public function create_entrada()
    {
        // Trae productos (muestra código, nombre, unidad y stock actual)
        $products = Product::orderBy('description')
            ->get(['id', 'code', 'description', 'extent', 'stock']);

        return view('movimientos.entrada', compact('products'));
    }

    /**
     * Guarda la ENTRADA y suma al stock
     */
    public function store_entrada(Request $request)
    {
        $validated = $request->validate(
            [
                'product_id'    => ['required', 'exists:products,id'],
                'date_products' => ['required', 'date'],
                'amount'        => ['required', 'numeric', 'min:0.0001'],
            ],
            [
                'product_id.required'    => 'Seleccione un producto.',
                'product_id.exists'      => 'El producto no existe.',
                'date_products.required' => 'La fecha es obligatoria.',
                'date_products.date'     => 'Fecha inválida.',
                'amount.required'        => 'La cantidad es obligatoria.',
                'amount.numeric'         => 'La cantidad debe ser numérica.',
                'amount.min'             => 'La cantidad debe ser mayor a 0.',
            ]
        );

        DB::transaction(function () use ($validated) {
            // 1) Bloquea el producto para actualización de stock
            $product = Product::where('id', $validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // 2) Crea el movimiento de ENTRADA
            $movement = new Movement();
            $movement->product_id    = $validated['product_id'];
            $movement->date_products = $validated['date_products'];
            $movement->type          = 'entrada';
            $movement->amount        = $validated['amount'];
            $movement->save();

            // 3) Suma al stock del producto
            // Si tu 'stock' es entero, redondea/castea según tu negocio.
            $product->stock = $product->stock + $validated['amount'];
            $product->save();
        });

        return redirect()
            ->route('movements.in.index')
            ->with('success', 'Entrada registrada y stock actualizado.');
    }

    public function ExportEntrada(Request $request)
    {
        $filters = $request->only(['q', 'product_id', 'date_from', 'date_to', 'sort', 'dir']);
        return Excel::download(new MovementsInExport($filters), 'entradas.xlsx');
    }

    /**
     * SALIDA
     */

    public function index_salida(Request $r)
    {
        // Base: SALIDAS con alias consistentes
        $base = Movement::query()
            ->where('movements.type', 'salida')
            ->join('products', 'movements.product_id', '=', 'products.id')
            ->select(
                'movements.*',
                'products.code as p_code',
                'products.description as p_description',
                'products.extent as p_extent',
                'products.warehouse as p_warehouse'
            );

        // Filtros
        if ($r->filled('q')) {
            $kw = trim($r->q);
            $base->where(function ($q) use ($kw) {
                $q->where('products.description', 'like', "%{$kw}%")
                    ->orWhere('products.code', 'like', "%{$kw}%");
            });
        }
        if ($r->filled('product_id'))   $base->where('movements.product_id', $r->product_id);
        if ($r->filled('delivered_to')) $base->where('movements.delivered_to', 'like', '%' . $r->delivered_to . '%');
        if ($r->filled('taken_by'))     $base->where('movements.taken_by', 'like', '%' . $r->taken_by . '%');
        if ($r->filled('date_from'))    $base->whereDate('movements.date_products', '>=', $r->date_from);
        if ($r->filled('date_to'))      $base->whereDate('movements.date_products', '<=', $r->date_to);
        if ($r->filled('area'))         $base->where('movements.area', $r->area); // NUEVO

        // Orden
        $sort = $r->get('sort', 'date_products');
        $dir  = $r->get('dir', 'desc');
        $allowed = ['date_products', 'amount', 'p_code', 'p_description', 'delivered_to', 'taken_by', 'area']; // NUEVO
        if (!in_array($sort, $allowed)) $sort = 'date_products';
        if (!in_array($dir, ['asc', 'desc'])) $dir = 'desc';
        $base->orderBy($sort, $dir);

        // KPIs
        $stats = [
            'count'      => (clone $base)->count(),
            'sum_amount' => (clone $base)->sum('amount'),
            'today'      => (clone $base)->whereDate('movements.date_products', now()->toDateString())->count(),
        ];

        $exits     = $base->paginate(12)->withQueryString();
        $products  = Product::orderBy('description')->get(['id', 'code', 'description']);
        $receivers = Movement::where('type', 'OUT')->whereNotNull('delivered_to')
            ->distinct()->orderBy('delivered_to')->pluck('delivered_to');
        $areas     = Movement::where('type', 'salida')->whereNotNull('area')  // NUEVO
            ->distinct()->orderBy('area')->pluck('area');

        return view('movimientos.lista_salida', compact('exits', 'products', 'receivers', 'stats', 'areas')); // NUEVO
    }

    public function create_salida()
    {
        $products = Product::orderBy('description')
            ->get(['id', 'code', 'description', 'extent', 'stock', 'warehouse']);

        return view('movimientos.salida', compact('products'));
    }

    /**
     * Guarda la SALIDA y resta del stock
     */
    public function store_salida(Request $request)
    {
        $validated = $request->validate(
            [
                'product_id'    => ['required', 'exists:products,id'],
                'date_products' => ['required', 'date'],
                'amount'        => ['required', 'numeric', 'min:0.0001'], // si tu stock es entero, cambia a integer|min:1
                'delivered_to'  => ['required', 'string', 'max:150'],     // Persona que recibe
                'area'         => ['required', 'string', 'max:150'],     // Área que recibe
                'taken_by'      => ['required', 'string', 'max:150'],     // Persona que retira
            ],
            [
                'product_id.required'    => 'Seleccione un producto.',
                'product_id.exists'      => 'El producto no existe.',
                'date_products.required' => 'La fecha es obligatoria.',
                'date_products.date'     => 'Fecha inválida.',
                'amount.required'        => 'La cantidad es obligatoria.',
                'amount.numeric'         => 'La cantidad debe ser numérica.',
                'amount.min'             => 'La cantidad debe ser mayor a 0.',
                'delivered_to.required'  => 'Indique a quién se entrega.',
                'area.required'         => 'Indique el área que recibe.',
                'taken_by.required'      => 'Indique quién retira.',
            ]
        );

        DB::transaction(function () use ($validated) {
            // 1) Bloquear el producto para cálculo de stock
            $product = Product::where('id', $validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // 2) Validar stock suficiente
            $available = (float)$product->stock;
            $toRemove  = (float)$validated['amount'];
            if ($toRemove > $available) {
                throw ValidationException::withMessages([
                    'amount' => "No hay stock suficiente. Disponible: {$available}.",
                ]);
            }

            // 3) Registrar movimiento de SALIDA
            Movement::create([
                'product_id'    => $validated['product_id'],
                'date_products' => $validated['date_products'],
                'type'          => 'salida',
                'amount'        => $validated['amount'],
                'delivered_to'  => $validated['delivered_to'],    // persona que recibe
                'area'         => $validated['area'],         // área que recibe
                'taken_by'      => $validated['taken_by'],       // quien registra
            ]);

            // 4) Descontar del stock
            $product->stock = $available - $toRemove;
            $product->save();
        });

        return redirect()
            ->route('movements.out.index')
            ->with('success', 'Salida registrada y stock actualizado.');
    }

    public function ExportSalida(Request $request)
    {
        $filters = $request->only([
            'q',
            'product_id',
            'area',
            'date_from',
            'date_to',
            'delivered_to',
            'taken_by',
            'sort',
            'dir'
        ]);
        return Excel::download(new MovementsOutExport($filters), 'salidas.xlsx');
    }
}
