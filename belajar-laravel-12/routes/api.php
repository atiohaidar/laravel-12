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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); // Gunakan sanctum untuk API auth

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class); // API Resource routes untuk user management
});

Route::get('/user', function () {
    return request()->user(); // Contoh route untuk mendapatkan user yang sedang login (untuk testing)
})->middleware('auth:sanctum');