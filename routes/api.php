<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\FrontController;
use App\Http\Controllers\Front\ShopeController;

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



Route::group(['prefix' => 'account'],function () {
    Route::group(['middleware' => 'guest'], function(){
        ############### User Login / Register ###############
        Route::post('/login', [AuthController::class, 'authenticate'])->name('api.account.authenticate');
        Route::post('/register', [AuthController::class, 'processRegister'])->name('api.account.processRegister');
        ############### User Login / Register ###############
    });
    
    Route::group(['middleware' => 'auth:api'], function(){
        ############### User Profile ###############
        Route::get('/profile', [AuthController::class, 'profile'])->name('api.account.profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.account.logout');

        Route::get('/my-orders', [AuthController::class, 'orders'])->name('api.account.orders');
        Route::get('/order-detail/{orderId}', [AuthController::class, 'orderDetail'])->name('api.account.orderDetail');

        Route::get('/my-wishlist', [AuthController::class, 'wishlist'])->name('api.account.wishlist');
        Route::post('/remove-product-from-wishlist', [AuthController::class, 'removeProductFromWishlist'])->name('api.account.removeProductFromWishlist');

        ############### Wishlist ###############
        Route::post('/add-to-wishlist', [FrontController::class, 'addToWishlist'])->name('api.front.addToWishlist');
        ############### Wishlist ###############

        ############### User Profile ###############
    });
    
});

############### Featured & Latest Products ###############
Route::get('/home', [FrontController::class, 'index'])->name('api.front.home');
############### Featured & Latest Products ###############


############### Products ###############
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopeController::class, 'index'])->name('api.shop.index');
############### Products ###############


############### Product page ###############
Route::get('/product/{slug}', [ShopeController::class, 'product'])->name('api.product.detail');
############### Product page ###############


############### Cart ###############
Route::get('/cart', [CartController::class, 'cart'])->name('api.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('api.addToCart');
Route::post('/update-cart', [CartController::class, 'updateCart'])->name('api.updateCart');
Route::post('/delete-item', [CartController::class, 'deleteItem'])->name('api.deleteItem.cart');
############### Cart ###############


############### Checkout ###############
// Route::middleware('auth:api')->group(function () {
    Route::get('/checkout', [CartController::class, 'checkout'])->name('api.checkout');
    Route::post('/process-checkout', [CartController::class, 'processCheckout'])->name('api.processCheckout');
    Route::get('/thanks/{orderId}', [CartController::class, 'thankYou'])->name('api.thankYou');
// });
############### Checkout ###############





















// Routes for guests (unauthenticated users)
// Route::post('/login', [AdminLoginController::class, 'authenticate'])->name('admin.api.login');

// // Routes for authenticated users
// Route::middleware('auth:sanctum')->group(function() {
//     Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.api.dashboard');
//     Route::post('/logout', [HomeController::class, 'logout'])->name('admin.api.logout');
// });










// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
