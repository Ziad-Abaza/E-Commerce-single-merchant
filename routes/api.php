<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes (no authentication required)
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::prefix('public')->name('public.')->group(function () {
    // Home routes
    Route::get('/home', [\App\Http\Controllers\Api\Public\HomeController::class, 'index'])->name('home');

    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\Api\SettingController::class, 'index'])->name('settings.index');

    // Public product routes
    Route::prefix('products')->name('products.')->controller(\App\Http\Controllers\Api\Public\ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });

    // Public review routes
    Route::prefix('reviews')->name('reviews.')->controller(\App\Http\Controllers\Api\User\ReviewController::class)->group(function () {
        Route::get('/{product_id}', 'index')->name('index');
        Route::get('/{product_id}/stats', 'getProductRatingStats')->name('stats');
    });

    // Contact form submission (public route)
    Route::post('/contact', [\App\Http\Controllers\Api\ContactMessageController::class, 'store'])->name('contact.store');

    Route::prefix('policies')->name('policies.')->controller(\App\Http\Controllers\Api\PolicyController::class)->group(function () {
        Route::get('/', 'index')->name('index'); // Get all policies
        Route::get('/{type}', 'show')->name('show'); // Get single policy by type
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

// Product Attributes Management
Route::prefix('product-attributes')
    ->name('product-attributes.')
    ->controller(\App\Http\Controllers\Dashboard\ProductAttributeController::class)
    ->group(function () {
        Route::get('/{productDetailId}', 'index')->name('index');
        Route::post('/{productDetailId}', 'update')->name('update');
        Route::delete('/{productDetailId}/attribute/{attributeId}', 'destroy')->name('destroy');
        Route::get('/{productDetailId}/category-attributes', 'getProductCategoryAttributes')->name('category-attributes');
        Route::get('/product/{productId}/variant-attributes', 'getVariantAttributes')->name('variant-attributes');
    });

Route::middleware('auth:sanctum')->group(function () {

    // Get authenticated user
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'message' => 'User retrieved successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'is_verified' => $user->hasVerifiedEmail(),
                'phone' => $user->phone,
                'address' => $user->address,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'avatar_url' => $user->getAvatarUrl() ?? null,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
            'code' => 200,
        ]);
    });

    // Profile routes
    Route::prefix('profile')->name('profile.')->controller(\App\Http\Controllers\Api\User\ProfileController::class)->group(function () {
        Route::get('/', 'show')->name('show');
        Route::post('/', 'update')->name('update');
        Route::post('/change-password', 'changePassword')->name('change-password');
        Route::get('/stats', 'getStats')->name('stats');
    });

    // Cart routes
    Route::prefix('carts')->name('carts.')->controller(\App\Http\Controllers\Api\User\CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::delete('/clear', 'clear')->name('clear');
        Route::post('/', 'store')->name('store');
        Route::post('/sync', 'sync')->name('sync');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Order routes
    Route::middleware('verified')->prefix('orders')->name('orders.')->controller(\App\Http\Controllers\Api\OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/cancel', 'markAsCancelled')->name('cancel');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    Route::prefix('reviews')->name('reviews.')->controller(\App\Http\Controllers\Api\User\ReviewController::class)->group(function () {
        Route::post('/{product_id}', 'store')->name('store');
        Route::get('/review/{id}', 'show')->name('show');
        Route::post('/review/{id}', 'update')->name('update');
        Route::delete('/review/{id}', 'destroy')->name('destroy');
    });

    // Wishlist routes
    Route::prefix('wishlist-categories')->name('wishlist-categories.')->controller(\App\Http\Controllers\Api\User\WishlistCategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/default', 'getDefaultCategory')->name('default');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    Route::prefix('wishlist-items')->name('wishlist-items.')->controller(\App\Http\Controllers\Api\User\WishlistItemController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/check', 'checkProductInWishlist')->name('check');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('/{id}/move', 'move')->name('move');
    });

});

require __DIR__ . '/dashboard.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/notification.php';