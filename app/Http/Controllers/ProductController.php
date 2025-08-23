<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $r)
    {
        $q = Product::query();

        if ($r->filled('q')) {
            $kw = $r->q;
            $q->where(function ($qq) use ($kw) {
                $qq->where('description', 'like', "%$kw%")
                    ->orWhere('code', 'like', "%$kw%");
            });
        }
        if ($r->filled('category'))   $q->where('categories', $r->category);
        if ($r->filled('warehouse'))  $q->where('warehouse',  $r->warehouse);

        $sort = $r->get('sort', 'date_products');
        $dir  = $r->get('dir', 'desc');
        $allowed = ['date_products', 'code', 'description', 'stock', 'categories', 'warehouse'];
        if (!in_array($sort, $allowed)) {
            $sort = 'date_products';
        }
        if (!in_array($dir, ['asc', 'desc'])) {
            $dir = 'desc';
        }
        $q->orderBy($sort, $dir);

        $products = $q->paginate(12)->withQueryString();

        // Para combos
        $categories = Product::select('categories')->distinct()->pluck('categories')->filter()->values();
        $warehouses = Product::select('warehouse')->distinct()->pluck('warehouse')->filter()->values();

        // KPIs opcionales
        $stats = [
            'total'      => Product::count(),
            'in_stock'   => Product::where('stock', '>', 0)->count(),
            'out_stock'  => Product::where('stock', 0)->count(),
        ];

        return view('productos.lista', compact('products', 'categories', 'warehouses', 'stats'));
    }


    //Revisa si el codigo ingresado ya existe
    public function checkCode(Request $request)
    {
        $code = $request->query('code');
        if (!is_numeric($code) || (int)$code < 1) {
            return response()->json(['exists' => false, 'valid' => false]);
        }
        $exists = \App\Models\Product::where('code', (int)$code)->exists();
        return response()->json(['exists' => $exists, 'valid' => true]);
    }

    public function create()
    {
        return view('productos.productos');
    }

    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate(
            [
                'code'          => ['required', 'integer', 'min:1', 'unique:products,code'],
                'description'   => ['required', 'string', 'min:3'],
                'stock'         => ['required', 'integer', 'min:0'],
                'categories'    => ['required', 'string', 'max:191'],
                'extent'        => ['required', 'string', 'max:50'],   // unidad de medida
                'warehouse' => ['required', 'string', 'max:100'],
                'date_products' => ['required', 'date'],               // formato Y-m-d
            ],
            [
                'code.required'        => 'El código es obligatorio.',
                'code.integer'         => 'El código debe ser un número entero.',
                'code.min'             => 'El código debe ser mayor o igual a 1.',
                'code.unique'          => 'Ya existe un producto con este código.',
                'description.required' => 'La descripción es obligatoria.',
                'description.min'      => 'La descripción debe tener al menos :min caracteres.',
                'stock.required'       => 'El stock es obligatorio.',
                'stock.integer'        => 'El stock debe ser un número entero.',
                'stock.min'            => 'El stock no puede ser negativo.',
                'categories.required'  => 'La categoría es obligatoria.',
                'categories.max'       => 'La categoría no debe exceder :max caracteres.',
                'extent.required'      => 'La unidad de medida es obligatoria.',
                'warehouse.required'   => 'El almacén es obligatorio.',
                'warehouse.max'        => 'El almacén no debe exceder :max caracteres.',
                'extent.max'           => 'La unidad de medida no debe exceder :max caracteres.',
                'date_products.required' => 'La fecha de alta es obligatoria.',
                'date_products.date'   => 'La fecha no es válida.',
            ]
        );

        // Crear registro
        Product::create($validated);

        // Redirigir (ajusta la ruta según tu listado)
        return redirect()->route('products.index')->with('success', 'Producto Agregado correctamente.');
    }
}
