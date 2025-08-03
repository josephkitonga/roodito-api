<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\UsersController;

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
Route::post('login', [UsersController::class, 'login']);
Route::post('logout', [UsersController::class, 'logout']);
Route::post('register', [UsersController::class, 'store']);

// User Search Routes (Public - no authentication required)
Route::get('search/users', [UsersController::class, 'search']);
Route::post('check/user-exists', [UsersController::class, 'checkExists']);

// User Management Routes (Protected)
Route::middleware('api.token')->group(function () {
    Route::get('me', [UsersController::class, 'me']);
    Route::apiResource('users', UsersController::class);
});

// Public update route for testing (remove in production)
Route::put('users/{user}/update', [UsersController::class, 'update']);

// Simple test route
Route::put('test-update/{id}', [UsersController::class, 'update']);
