<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/data', [ProductController::class, 'getData'])->name('products.data');
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
Route::post('/products/update', [ProductController::class, 'update'])->name('products.update');
Route::post('/products/delete', [ProductController::class, 'destroy'])->name('products.delete');