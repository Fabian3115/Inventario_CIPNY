<?php

use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ComputationController;
use App\Http\Controllers\ComputedMovementController;

use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, '__invoke'])->name('welcome');


//Productos
Route::get('products',   [ProductController::class, 'index'])->name('products.index');
Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('products/store',  [ProductController::class, 'store'])->name('products.store');

Route::get('/products/check-code', [ProductController::class, 'checkCode'])->name('products.checkCode');
// NUEVA: entrega el siguiente código sugerido (max(code) + 1)
Route::get('products/next-code', [ProductController::class, 'nextCode'])->name('products.nextCode');

//Movimientos
//Crear Movimiento
Route::get('movements', [MovementController::class, 'index'])->name('movements.index');
Route::get('movements/create', [MovementController::class, 'create'])->name('movements.create');
Route::post('movements', [MovementController::class, 'store'])->name('movements.store');

//Editar Movimiento
Route::get('movements/{movement}/edit', [MovementController::class, 'edit'])->name('movements.edit');
Route::put('movements/{movement}', [MovementController::class, 'update'])->name('movements.update');

//Exportar Movimiento
Route::get('movements/export', [MovementController::class, 'export'])->name('movements.export');

//Importar Movimiento
Route::get('movements/import', [MovementController::class, 'importForm'])->name('movements.import.form');
Route::post('movements/import', [MovementController::class, 'import'])->name('movements.import');
Route::get('movements/template', [MovementController::class, 'template'])->name('movements.template');

//Historial de Movimientos
Route::get('movements/history/{product}', [MovementController::class, 'history'])
    ->name('movements.history');

//Eliminar Movimiento
Route::delete('movements/{movement}', [MovementController::class, 'destroy'])->name('movements.destroy');




// Computations (equipos)
// Opción A: rutas sueltas
Route::get('computations/create', [ComputationController::class, 'create'])->name('computations.create');
Route::post('computations',        [ComputationController::class, 'store'])->name('computations.store');


Route::get('computations/{computation}/edit', [ComputationController::class, 'edit'])->name('computations.edit');
Route::put('computations/{computation}', [ComputationController::class, 'update'])->name('computations.update');

// Computed Movements (movimientos de equipo)
Route::get('computed_movements/create', [ComputedMovementController::class, 'create'])->name('computed_movements.create');
Route::post('computed_movements',        [ComputedMovementController::class, 'store'])->name('computed_movements.store');
Route::get('computed_movements/{movement}/edit', [ComputedMovementController::class, 'edit'])->name('computed_movements.edit');
Route::put('computed_movements/{movement}', [ComputedMovementController::class, 'update'])->name('computed_movements.update');

Route::get('computations', [ComputationController::class, 'index'])->name('computations.index');
Route::get('computed_movements', [ComputedMovementController::class, 'index'])->name('computed_movements.index');
