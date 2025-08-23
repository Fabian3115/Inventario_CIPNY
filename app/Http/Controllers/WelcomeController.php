<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Movement; // Ajusta si tu modelo se llama distinto
use Illuminate\Support\Facades\Schema;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $hoy  = Carbon::today();        // respeta timezone app
        $from = Carbon::today()->subDays(30);

        // Total de productos
        $products_total = Product::count();

        // Entradas / Salidas últimos 30 días
        $ins_30d = Movement::where('type', 'entrada')
            ->whereBetween('date_products', [$from, $hoy])
            ->sum('amount');

        $outs_30d = Movement::where('type', 'salida')
            ->whereBetween('date_products', [$from, $hoy])
            ->sum('amount');

        // Stock bajo:
        // - Si existe columna min_stock: stock <= min_stock
        // - Si no, usa un umbral global por defecto (p. ej. 10)
        $defaultThreshold = config('inventory.low_stock_threshold', 10);

        $low_stock = Product::when(
            Schema::hasColumn('products', 'min_stock'),
            fn($q) => $q->whereColumn('stock', '<=', 'min_stock'),
            fn($q) => $q->where('stock', '<=', $defaultThreshold)
        )->count();

        $stats = compact('products_total', 'ins_30d', 'outs_30d', 'low_stock');

        $defaultThreshold = config('inventory.low_stock_threshold', 10);

        $out_of_stock = Product::select('id', 'code', 'description', 'stock')
            ->when(
                Schema::hasColumn('products', 'min_stock'),
                fn($q) => $q->whereColumn('stock', '<=', 'min_stock'),
                fn($q) => $q->where('stock', '<=', $defaultThreshold)
            )
            ->orderBy('stock')
            ->limit(200) // por si hay muchos
            ->get();

        return view('welcome', compact('stats', 'out_of_stock'));
    }
}
