<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::prefix('public')->name('public.')->group(function () {
    // Home routes
    Route::get('/home', [\App\Http\Controllers\Api\Public\HomeController::class, 'index'])->name('home');

    // Public product routes
    Route::prefix('products')->name('products.')->controller(\App\Http\Controllers\Api\Public\ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });

    // Public category routes
    Route::prefix('categories')->name('categories.')->controller(\App\Http\Controllers\Api\CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/parents', 'parents')->name('parents');
        Route::get('/tree', 'tree')->name('tree');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/products', 'withProducts')->name('with-products');
        Route::get('/{id}/subcategories', 'subcategories')->name('subcategories');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return response()->json([
            'message' => 'User retrieved successfully.',
            'data' => $request->user(),
            'code' => 200,
        ]);
    });

    // Profile routes
    // Profile routes
    Route::middleware('auth:sanctum')->prefix('profile')->name('profile.')->controller(\App\Http\Controllers\Api\User\ProfileController::class)->group(function () {
        Route::get('/', 'show')->name('show');
        Route::post('/', 'update')->name('update');
        Route::post('/change-password', 'changePassword')->name('change-password');
        Route::get('/stats', 'getStats')->name('stats');
    });

    // Cart routes
    Route::prefix('carts')->name('carts.')->controller(\App\Http\Controllers\Api\User\CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::delete('/clear', 'clear')->name('clear');
    });

    // Order routes
    Route::prefix('orders')->name('orders.')->controller(\App\Http\Controllers\Api\OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Payment routes
    Route::prefix('payments')->name('payments.')->controller(\App\Http\Controllers\Api\PaymentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Review routes
    Route::prefix('reviews')->name('reviews.')->controller(\App\Http\Controllers\Api\User\ReviewController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Wishlist routes
    Route::prefix('wishlist-categories')->name('wishlist-categories.')->controller(\App\Http\Controllers\Api\User\WishlistCategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/default', 'getDefaultCategory')->name('default');
    });

    Route::prefix('wishlist-items')->name('wishlist-items.')->controller(\App\Http\Controllers\Api\User\WishlistItemController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('/{id}/move', 'move')->name('move');
        Route::get('/check', 'checkProductInWishlist')->name('check');
    });
});

require __DIR__ . '/dashboard.php';
require __DIR__ . '/auth.php';
