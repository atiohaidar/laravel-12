<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome'); // Atau dashboard jika sudah login
});

// Routes Autentikasi
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes User Management & API (Perlu Middleware Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/users/profile', [UserController::class, 'profile'])->name('users.profile'); // ini harus sebelum resource, biar kebaca
    Route::resource('users', UserController::class); // CRUD lengkap untuk user
    
    // Dashboard routes
    Route::get('/dashboard', function () {
        return view('dashboard'); // Halaman dashboard
    })->name('dashboard');
    Route::get('/api-docs', function () {
        return view('api.documentation');
    })->name('api.docs');
    
    
    // Token management
    Route::get('/tokens', [UserController::class, 'tokens'])->name('tokens.index');
    Route::post('/tokens', [UserController::class, 'createToken'])->name('tokens.create');
    Route::delete('/tokens/{token}', [UserController::class, 'destroyToken'])->name('tokens.destroy');
    
    // Route for sending email
    Route::post('/users/{id}/send-email', [UserController::class, 'sendEmail'])->name('users.send-email');
    
    // Route for sending telegram message
    Route::post('/users/{id}/send-telegram', [UserController::class, 'sendTelegram'])->name('users.send-telegram');
});