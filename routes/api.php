<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

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
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });
});

require __DIR__ . '/dashboard.php';
require __DIR__ . '/auth.php';
