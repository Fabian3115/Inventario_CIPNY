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


Route::get('/products/check-code', [ProductController::class, 'checkCode'])
    ->name('products.checkCode');

//Movimientos
//ENTRADA
Route::get('/movements/in/lista', [MovementController::class, 'index_entrada'])->name('movements.in.index');
Route::get('/movements/in/create', [MovementController::class, 'create_entrada'])->name('movements.in.create');
Route::post('/movements/in',        [MovementController::class, 'store_entrada'])->name('movements.in.store');

//SALIDA
Route::get('/movements/out/lista', [MovementController::class, 'index_salida'])->name('movements.out.index');
Route::get('/movements/out/create', [MovementController::class, 'create_salida'])->name('movements.out.create');
Route::post('/movements/out',    [MovementController::class, 'store_salida'])->name('movements.out.store');

//EXPOTACION DE EXCEL
Route::get('/movements/in/export', [MovementController::class, 'ExportEntrada'])->name('movements.in.export'); //Entrada
Route::get('/movements/out/export', [MovementController::class, 'ExportSalida'])->name('movements.out.export'); //Salida
