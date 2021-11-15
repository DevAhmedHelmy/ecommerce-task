<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ApiAuthController;
// use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// dd(request()->header());
Route::group(['middleware' => ['cors']], function () {

    // ...

    // public routes
    Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
    Route::post('/register', [ApiAuthController::class, 'register'])->name('register.api');

    // ...

    Route::post('carts/incrmentItem/{cartId}/{itemId}', [App\Http\Controllers\CartController::class, 'incrmentItem']);
    Route::post('carts/decrmentItem/{cartId}/{itemId}', [App\Http\Controllers\CartController::class, 'decrmentItem']);
    Route::post('carts/removeItem/{cartId}/{itemId}', [App\Http\Controllers\CartController::class, 'removeItem']);
    Route::get('carts/getAuthUser', [App\Http\Controllers\CartController::class, 'getAuthUser']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('carts', CartController::class);

    Route::middleware('auth:sanctum')->group(function () {
        // our routes to be protected will go in here
        Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');
    });
});
