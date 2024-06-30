<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\SubCategoryController;

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


// Routes for guests (unauthenticated users)
Route::post('/login', [AdminLoginController::class, 'authenticate'])->name('admin.api.login');

// Routes for authenticated users
Route::middleware(['auth:sanctum', 'admin.check'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.api.dashboard');
    Route::post('/logout', [HomeController::class, 'logout'])->name('admin.api.logout');

    ################### Category ################### 
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('api.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('api.categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('api.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('api.categories.delete');
    ################### Category ################### 
    
    ################### Sub Category ################### 
    Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('api.sub-categories.index');
    Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('api.sub-categories.create');
    Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('api.sub-categories.store');
    Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('api.sub-categories.edit');
    Route::put('/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('api.sub-categories.update');
    Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('api.sub-categories.delete');
    ################### Sub Category ################### 
    
    ################### Products ################### 
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/create', [ProductController::class, 'create']);
    Route::post('/products/store', [ProductController::class, 'store']);
    Route::get('/products/{product}/edit', [ProductController::class, 'edit']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
    Route::get('/get-products', [ProductController::class, 'getProducts']);
    Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('api.product-subcategories.index');

    Route::post('/product-images/update', [ProductImageController::class, 'update'])->name('api.product-images.update');
    Route::delete('/product-images', [ProductImageController::class, 'destroy'])->name('api.product-images.destroy');
    ################### Products ################### 
    
    ################### Orders ################### 
    Route::get('/orders', [OrderController::class, 'index'])->name('api.orders.index');
    Route::get('/order/{id}', [OrderController::class, 'detail'])->name('api.orders.detail');
    Route::post('/order/change-status/{id}', [OrderController::class, 'changeOrderStatus'])->name('api.orders.changeOrderStatus');
    ################### Orders ################### 
    
    ################### temp-images.create ################### 
    Route::post('/upload-image-temp', [TempImagesController::class, 'create'])->name('api.temp-images.create'); 
    

    ################### Slug ################### 
    Route::get('/getSlug', function (Request $request) {
        $slug = '';
        if (!empty($request->titel)) {
            $slug = Str::slug($request->titel);
        }
        return response()->json([
            'status' => true,
            'slug' => $slug
        ]);
    })->name('getSlug');

});



















// Route::middleware(['auth:sanctum', 'admin.check'])->group(function () {
//     Route::get('/dashboard', [HomeController::class, 'index']);
//     Route::post('/logout', [HomeController::class, 'logout']);

//     ################### Category ################### 
//     Route::get('/categories', [CategoryController::class, 'index']);
//     ################### Category ################### 
// });










// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
