<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Admin-Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('dashboard')->name('dashboard.')->group(function () {

    // Dashboard Home
    Route::controller(\App\Http\Controllers\Dashboard\HomeController::class)->group(function () {
        Route::get('/', 'statistics')->name('home');
        Route::get('/statistics', 'statistics')->name('statistics');
        Route::get('/recent-orders', 'recentOrders')->name('recent-orders');
        Route::get('/recent-products', 'recentProducts')->name('recent-products');
        Route::get('/recent-reviews', 'recentReviews')->name('recent-reviews');
        Route::get('/sales-analytics', 'salesAnalytics')->name('sales-analytics');
        Route::get('/revenue-analytics', 'revenueAnalytics')->name('revenue-analytics');
        Route::get('/user-analytics', 'userAnalytics')->name('user-analytics');
    });

    // Users Management
    Route::middleware(['can:manage_users'])
        ->prefix('users')
        ->name('users.')
        ->controller(\App\Http\Controllers\Dashboard\UserController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
        });

    Route::middleware(['can:manage_roles'])
        ->prefix('roles')
        ->name('roles.')
        ->controller(\App\Http\Controllers\Dashboard\RoleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/permissions', 'permissions')->name('permissions');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
        });

    // Products Management
    Route::middleware(['can:manage_products'])
        ->prefix('products')
        ->name('products.')
        ->controller(\App\Http\Controllers\Dashboard\ProductController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
        });

    // Product Details Management
    Route::middleware(['can:manage_products'])
        ->prefix('product-details')
        ->name('product_details.')
        ->controller(\App\Http\Controllers\Dashboard\ProductDetailController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
        });

    // Categories Management
    Route::middleware(['can:manage_categories'])
        ->prefix('categories')
        ->name('categories.')
        ->controller(\App\Http\Controllers\Dashboard\CategoryController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
        });

    // Orders Management
    Route::middleware(['can:manage_orders'])
        ->prefix('orders')
        ->name('orders.')
        ->controller(\App\Http\Controllers\Dashboard\OrderController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/statistics', 'statistics')->name('statistics');
            Route::get('/status/{status}', 'byStatus')->name('by-status')->where('status', '[a-zA-Z0-9_-]+');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::post('/{id}/status', 'updateStatus')->name('update-status')->where('id', '[0-9]+');
            Route::post('/{id}/cancel', 'cancel')->name('cancel')->where('id', '[0-9]+');
            Route::get('/{id}/items', 'orderItems')->name('items')->where('id', '[0-9]+');
            Route::get('/{id}/history', 'history')->name('history')->where('id', '[0-9]+');
        });

    // Payments Management
    Route::middleware(['can:manage_payments'])
        ->prefix('payments')
        ->name('payments.')
        ->controller(\App\Http\Controllers\Dashboard\PaymentController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/statistics', 'statistics')->name('statistics');
            Route::get('/status/{status}', 'byStatus')->name('by-status')->where('status', '[a-zA-Z0-9_-]+');
            Route::get('/failed', 'failedPayments')->name('failed');
            Route::get('/revenue-analytics', 'revenueAnalytics')->name('revenue-analytics');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::post('/{id}/status', 'updateStatus')->name('update-status')->where('id', '[0-9]+');
            Route::post('/{id}/refund', 'refund')->name('refund')->where('id', '[0-9]+');
        });

    // Reviews Management
    Route::middleware(['can:manage_reviews'])
        ->prefix('reviews')
        ->name('reviews.')
        ->controller(\App\Http\Controllers\Dashboard\ReviewController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/statistics', 'statistics')->name('statistics');
            Route::get('/pending', 'pending')->name('pending');
            Route::get('/product/{productId}', 'byProduct')->name('by-product')->where('productId', '[0-9]+');
            Route::get('/rating/{rating}', 'byRating')->name('by-rating')->where('rating', '[1-5]');
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::post('/{id}/approve', 'approve')->name('approve')->where('id', '[0-9]+');
            Route::post('/{id}/reject', 'reject')->name('reject')->where('id', '[0-9]+');
            Route::post('/bulk-approve', 'bulkApprove')->name('bulk-approve');
            Route::post('/bulk-reject', 'bulkReject')->name('bulk-reject');
            Route::delete('/{id}', 'destroy')->name('destroy')->where('id', '[0-9]+');
        });

});
