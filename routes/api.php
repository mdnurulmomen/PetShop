<?php

use App\Http\Controllers\Api\V1\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\PromotionController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\CategoryController;

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

// Brands
Route::prefix('v1')->group(function () {

    Route::get('/brands', [BrandController::class, 'getBrandList'])->name('brands.index');

    Route::prefix('/brand')->group(function () {

        Route::get('/{uuid}', [BrandController::class, 'showBrand'])->name('brands.show');

        Route::middleware(['auth.jwt'])->group(function () {

            Route::post('/create', [BrandController::class, 'storeBrand'])->name('brands.store');
            Route::put('/{uuid}', [BrandController::class, 'updateBrand'])->name('brands.update');
            Route::delete('/{uuid}', [BrandController::class, 'deleteBrand'])->name('brands.destroy');

        });
    });

});

// Category
Route::prefix('v1')->group(function () {

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    Route::prefix('/category')->group(function () {

        Route::get('/{uuid}', [CategoryController::class, 'show'])->name('categories.show');

        Route::middleware(['auth.jwt'])->group(function () {

            Route::post('/create', [CategoryController::class, 'storeCategory'])->name('categories.store');
            Route::put('/{uuid}', [CategoryController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/{uuid}', [CategoryController::class, 'deleteCategory'])->name('categories.destroy');

        });
    });

});

// Main
Route::name('main.')->group(function () {

    Route::prefix('v1/main')->group(function () {

        Route::get('/promotions', [PromotionController::class, 'getPromotionList'])->name('promotions.index');
        Route::get('/blog', [PostController::class, 'getPostList'])->name('blogs.index');
        Route::get('/blog/{uuid}', [PostController::class, 'getPost'])->name('blogs.show');

    });

});

// Admin
Route::name('admin.')->group(function () {

    Route::prefix('v1/admin')->group(function () {

        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::middleware(['auth.jwt', 'admin'])->group(function () {

            Route::post('/create', [UserController::class, 'store'])->name('users.store');
            Route::get('/user-listing', [AdminController::class, 'getUserList'])->name('users.index');
            Route::put('/user-edit/{uuid}', [AdminController::class, 'updateUser'])->name('users.update');
            Route::delete('/user-delete/{uuid}', [AdminController::class, 'deleteUser'])->name('users.destroy');
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        });

    });

});

Route::name('user.')->group(function () {

    Route::prefix('v1/user')->group(function () {

        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/forgot-password', [AuthController::class, 'getResetToken'])->name('reset-token');
        Route::post('/reset-password-token', [AuthController::class, 'resetPassword'])->name('reset-password');

        Route::middleware(['auth.jwt'])->group(function () {

            Route::middleware(['admin'])->group(function () {

                Route::post('/create', [UserController::class, 'store'])->name('store');
                Route::delete('/', [AuthController::class, 'delete'])->name('destroy');

            });

            Route::get('/', [AuthController::class, 'me'])->name('show');
            Route::get('/orders', [UserController::class, 'getMyOrders'])->name('orders.index');
            Route::put('/edit', [UserController::class, 'update'])->name('update');
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        });

    });

});

Route::fallback(function(){

    return response()->json([
        'success' => false,
        'data' => [],
        'error' => 'No endpoint found',
        'errors' => [],
        "extra" => []
    ], 404);

});


