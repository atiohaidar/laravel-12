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
    
    // Event API routes
    Route::apiResource('events', \App\Http\Controllers\Api\EventController::class);
    Route::apiResource('event-categories', \App\Http\Controllers\Api\EventCategoryController::class);
    
    // Event registration API routes
    Route::post('/events/{event}/register', [\App\Http\Controllers\Api\EventRegistrationController::class, 'register']);
    Route::get('/events/registrations/my', [\App\Http\Controllers\Api\EventController::class, 'myRegistrations']);
    Route::put('/events/registrations/{registration}/cancel', [\App\Http\Controllers\Api\EventRegistrationController::class, 'cancel']);
    Route::put('/events/registrations/{registration}/check-in', [\App\Http\Controllers\Api\EventRegistrationController::class, 'checkIn']);
    Route::get('/events/{event}/attendees', [\App\Http\Controllers\Api\EventController::class, 'attendees']);
});

Route::get('/user', function () {
    return request()->user(); // Contoh route untuk mendapatkan user yang sedang login (untuk testing)
})->middleware('auth:sanctum');