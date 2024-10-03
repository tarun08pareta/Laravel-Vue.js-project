<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConditionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::resource('products', ProductController::class)->except(['show']);
Route::get('/products', [ProductController::class, 'getFilteredProducts']);

Route::resource('categories', CategoryController::class);
Route::get('/products/{id}', [ProductController::class, 'getProductById'])->where('id', '[0-9]+');;


// Route::get('/products/categories/{categoryId}', [ProductController::class, 'getProductsByCategory'])->name('products.getProductsByCategory');
// Route::get('/products/conditions/{conditionId}', [ProductController::class, 'getProductsByCondition']);


Route::get('/conditions', [ConditionController::class, 'index']);
Route::post('/products/import', [ProductController::class, 'import']);
Route::get('/products/export', [ProductController::class, 'export']);
// Route::post('/api/export-products', [ProductController::class, 'exportProducts']);


