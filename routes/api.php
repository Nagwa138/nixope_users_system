<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

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


Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);


Route::group(
    [
        'middleware' => ['auth:sanctum']
    ],
    function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::resource('users', UserController::class);
    }
);
