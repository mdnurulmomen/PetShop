<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\UserController;

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

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::prefix('v1/user')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware(['auth:api'])->group(function () {

        Route::get('/', [AuthController::class, 'me']);
        Route::delete('/', [AuthController::class, 'delete']);
        Route::get('/orders', [UserController::class, 'getUserOrders']);
        Route::put('/edit', [UserController::class, 'editUser']);

    });

});


