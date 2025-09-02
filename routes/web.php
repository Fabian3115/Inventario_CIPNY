<?php

use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
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
