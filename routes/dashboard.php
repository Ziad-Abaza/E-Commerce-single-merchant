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
        Route::get('/overview', 'overview')->name('overview');
    });

    // Users Management
    Route::middleware(['can:manage_users'])
        ->prefix('users')
        ->name('users.')
        ->controller(\App\Http\Controllers\Dashboard\UserController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/trashed', 'trashed')->name('trashed');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force', 'forceDelete')->name('forceDelete');
            Route::post('{id}/assign-role', 'assignRole')->name('assignRole');
            Route::post('{id}/remove-role', 'removeRole')->name('removeRole');
        });

    Route::middleware(['can:manage_roles'])
        ->prefix('roles')
        ->name('roles.')
        ->controller(\App\Http\Controllers\Dashboard\RoleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/permissions', 'permissions')->name('permissions');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    // Products Management
    Route::middleware(['can:manage_products'])
        ->prefix('products')
        ->name('products.')
        ->controller(\App\Http\Controllers\Dashboard\ProductController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/trash', 'trash')->name('trash');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force-delete', 'forceDelete')->name('force-delete');
        });

    // Product Details Management
    Route::middleware(['can:manage_products'])
        ->prefix('products/{product}/details')
        ->name('products.details.')
        ->controller(\App\Http\Controllers\Dashboard\ProductDetailController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/trash', 'trashed')->name('trash');
            Route::get('/{detail}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{detail}', 'update')->name('update');
            Route::delete('/{detail}', 'destroy')->name('destroy');
            Route::post('/{detail}/restore', 'restore')->name('restore');
            Route::delete('/{detail}/force-delete', 'forceDestroy')->name('force-delete');
        });

    // Categories Management
    Route::middleware(['can:manage_categories'])
        ->prefix('categories')
        ->name('categories.')
        ->controller(\App\Http\Controllers\Dashboard\CategoryController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    // Orders Management
    Route::middleware(['can:manage_orders'])
        ->prefix('orders')
        ->name('orders.')
        ->controller(\App\Http\Controllers\Dashboard\OrderController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/handle-expired', 'handleExpiredPendingOrders')->name('handleExpired');
            Route::get('/{id}', 'show')->name('show');
            Route::match(['post', 'patch'], '/{id}', 'update')->name('update');
            Route::patch('/{id}/status', 'updateStatus')->name('updateStatus');
            Route::patch('/{id}/cancel', 'cancel')->name('cancel');
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
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'update')->name('update');
            Route::post('/{id}/status', 'updateStatus')->name('update-status');
            Route::post('/{id}/refund', 'refund')->name('refund');
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
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'update')->name('update');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
            Route::post('/bulk-approve', 'bulkApprove')->name('bulk-approve');
            Route::post('/bulk-reject', 'bulkReject')->name('bulk-reject');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
});
