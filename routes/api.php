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
    Route::controller(\App\Http\Controllers\HomeController::class)->group(function () {
        Route::get('/featured-products', 'featuredProducts')->name('featured-products');
        Route::get('/latest-products', 'latestProducts')->name('latest-products');
        Route::get('/categories', 'categories')->name('categories');
        Route::get('/search', 'searchProducts')->name('search');
        Route::get('/products/category/{categoryId}', 'productsByCategory')->name('products-by-category')->where('categoryId', '[0-9]+');
        Route::get('/products/{id}', 'showProduct')->name('product')->where('id', '[0-9]+');
        Route::get('/statistics', 'statistics')->name('statistics');
    });

    // Public product routes
    Route::prefix('products')->name('products.')->controller(\App\Http\Controllers\Api\ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/featured', 'featured')->name('featured');
        Route::get('/latest', 'latest')->name('latest');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::get('/{id}/related', 'related')->name('related')->where('id', '[0-9]+');
        Route::get('/{id}/reviews', 'reviews')->name('reviews')->where('id', '[0-9]+');
        Route::get('/category/{categoryId}', 'byCategory')->name('by-category')->where('categoryId', '[0-9]+');
    });

    // Public category routes
    Route::prefix('categories')->name('categories.')->controller(\App\Http\Controllers\Api\CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/parents', 'parents')->name('parents');
        Route::get('/tree', 'tree')->name('tree');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::get('/{id}/products', 'withProducts')->name('with-products')->where('id', '[0-9]+');
        Route::get('/{id}/subcategories', 'subcategories')->name('subcategories')->where('id', '[0-9]+');
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
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'show'])->name('show');
        Route::post('/', [\App\Http\Controllers\Api\ProfileController::class, 'update'])->name('update');
        Route::post('/change-password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword'])->name('change-password');
    });

    // Cart routes
    Route::prefix('carts')->name('carts.')->controller(\App\Http\Controllers\Api\CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
    });

    // Order routes
    Route::prefix('orders')->name('orders.')->controller(\App\Http\Controllers\Api\OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
    });

    // Payment routes
    Route::prefix('payments')->name('payments.')->controller(\App\Http\Controllers\Api\PaymentController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
    });

    // Review routes
    Route::prefix('reviews')->name('reviews.')->controller(\App\Http\Controllers\Api\ReviewController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
    });

    // Wishlist routes
    Route::prefix('wishlist-categories')->name('wishlist-categories.')->controller(\App\Http\Controllers\Api\WishlistCategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
    });

    Route::prefix('wishlist-items')->name('wishlist-items.')->controller(\App\Http\Controllers\Api\WishlistItemController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
        Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
    });
});

require __DIR__ . '/dashboard.php';
require __DIR__ . '/auth.php';
