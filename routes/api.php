<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('saf-bundle', [App\Http\Controllers\SafBundleController::class, 'index']);

// Password Reset Routes 
Route::post('password/reset-link', [PasswordResetController::class, 'sendResetLink']);

// Authentication Routes
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout']);

// User Search Routes (Public - no authentication required)
Route::get('users/search', [UserController::class, 'search']);
Route::post('users/check-exists', [UserController::class, 'checkExists']);

// User Management Routes (Protected)
Route::middleware('api.token')->group(function () {
    Route::get('me', [UserController::class, 'me']);
    Route::apiResource('users', UserController::class);
});
