<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\SettingController as SiteSettingController;
use App\Http\Controllers\Dashboard\PolicyController;

/*
|--------------------------------------------------------------------------
| Admin-Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {

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

    Route::middleware(['auth', 'can:manage_roles'])
        ->prefix('roles')
        ->name('roles.')
        ->controller(\App\Http\Controllers\Dashboard\RoleController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    // Policies Management
    Route::middleware(['can:manage_settings'])
        ->prefix('policies')
        ->name('policies.')
        ->controller(PolicyController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggleStatus');
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

    //  promo codes management
    Route::prefix('promo-codes')
        ->name('promo-codes.')
        ->controller(\App\Http\Controllers\Dashboard\PromoCodeController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update'); // Using POST for update as per your controller
            Route::delete('/{id}', 'destroy')->name('destroy');
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

    // Attributes Management
    Route::middleware(['can:manage_products']) // Or use a new 'can:manage_attributes' permission
        ->prefix('attributes')
        ->name('attributes.')
        ->controller(\App\Http\Controllers\Dashboard\AttributeController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    // Custom route to get attributes for a specific category
    Route::get('/categories/{categoryId}/attributes', [\App\Http\Controllers\Dashboard\AttributeController::class, 'getByCategory'])
        ->middleware('can:manage_products')
        ->name('categories.attributes');

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

    // Reviews Management
    Route::middleware(['can:manage_reviews'])
        ->prefix('reviews')
        ->name('reviews.')
        ->controller(\App\Http\Controllers\Dashboard\ReviewController::class)
        ->group(function () {

            // Routes without parameters first
            Route::get('/', 'index')->name('index');
            Route::post('/bulk-activate', 'bulkActivate')->name('bulk-activate');
            Route::post('/bulk-deactivate', 'bulkDeactivate')->name('bulk-deactivate');
            Route::post('/bulk-approve', 'bulkApprove')->name('bulk-approve');
            Route::post('/bulk-reject', 'bulkReject')->name('bulk-reject');
            Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');

            // Routes with {id} parameters
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'update')->name('update');
            Route::post('/{id}/toggle-active', 'toggleActive')->name('toggle-active');
            Route::post('/{id}/activate', 'activate')->name('activate');
            Route::post('/{id}/deactivate', 'deactivate')->name('deactivate');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

    // Contact Messages Management
    Route::middleware(['can:manage_contact_messages'])
        ->prefix('contact-messages')
        ->name('contact-messages.')
        ->controller(\App\Http\Controllers\Dashboard\ContactMessageController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/trashed', 'trashed')->name('trashed');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::post('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/restore', 'restore')->name('restore');
            Route::delete('/{id}/force', 'forceDelete')->name('forceDelete');
            Route::patch('/{id}/status', 'updateStatus')->name('updateStatus');
        });

    // Settings Management
    Route::middleware(['can:manage_settings'])
        ->prefix('settings')
        ->name('settings.')
        ->controller(SiteSettingController::class)
        ->group(function () {
            // Routes without parameters first
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::post('/bulk-update', 'bulkUpdate')->name('bulk-update');

            // Public settings
            Route::get('/public', 'public')->name('public');

            // Routes with {setting} parameter
            Route::get('/{setting}', 'show')->name('show');
            Route::match(['post', 'patch'], '/{setting}', 'update')->name('update');
            Route::delete('/{setting}', 'destroy')->name('destroy');
        });
});
