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

        // =========================
        // KPIs
        // =========================

        // Total de productos
        $products_total = Product::count();

        // Entradas / Salidas últimos 30 días (ajusta a whereDate si tu columna es DATE)
        $ins_30d = Movement::where('type', 'entrada')
            ->whereBetween('date_products', [$from, $hoy])
            ->sum('amount');

        $outs_30d = Movement::where('type', 'salida')
            ->whereBetween('date_products', [$from, $hoy])
            ->sum('amount');

        // Stock bajo para KPI:
        // - Si existe columna min_stock: stock <= min_stock
        // - Si no, usa umbral global (config/inventory.php -> low_stock_threshold)
        $defaultThreshold = config('inventory.low_stock_threshold', 10);

        $low_stock = Product::when(
            Schema::hasColumn('products', 'min_stock'),
            fn($q) => $q->whereColumn('stock', '<=', 'min_stock'),
            fn($q) => $q->where('stock', '<=', $defaultThreshold)
        )->count();

        $stats = compact('products_total', 'ins_30d', 'outs_30d', 'low_stock');

        // =========================
        // Datos para el modal “Reposición”
        // (requisito: mostrar < 2 y = 0)
        // =========================

        // AGOTADOS: stock <= 0
        $agotados = Product::select('id', 'code', 'description', 'stock', 'warehouse') // o 'warehouse'
            ->where('stock', '<=', 0)->orderBy('code')->limit(200)->get();

        // BAJO STOCK 2: 0 < stock < 3
        $bajo_stock_2 = Product::select('id', 'code', 'description', 'stock', 'warehouse') // o 'warehouse'
            ->where('stock', '>', 0)->where('stock', '<',3)
            ->orderBy('stock')->orderBy('code')->limit(200)->get();
        // También puedes mantener tu colección original (opcional):
        // $out_of_stock = $agotados->concat($bajo_stock_2);

        return view('welcome', [
            'stats'         => $stats,
            // 'out_of_stock' => $out_of_stock, // opcional si aún lo usas en otros lugares
            'agotados'      => $agotados,
            'bajo_stock_2'  => $bajo_stock_2,
        ]);
    }
}
