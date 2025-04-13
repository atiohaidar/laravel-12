<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

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

// Add Broadcast routes for WebSocket authentication

Route::get('/', function () {
    return view('welcome'); // Atau dashboard jika sudah login
});

// Routes Autentikasi
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

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
    
    // Wallet routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/topup', [WalletController::class, 'showTopUpForm'])->name('wallet.topup.form');
    Route::post('/wallet/topup', [WalletController::class, 'topUp'])->name('wallet.topup');
    Route::get('/wallet/transfer', [WalletController::class, 'showTransferForm'])->name('wallet.transfer.form');
    Route::post('/wallet/transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::get('/wallet/transactions/{id}', [WalletController::class, 'transactionDetail'])->name('wallet.transaction.detail');
    
    // Marketplace routes
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->name('index');
        Route::get('/product/{product}', [MarketplaceController::class, 'show'])->name('show');
        Route::post('/product/{product}/confirm', [MarketplaceController::class, 'confirmPurchase'])->name('confirm');
        Route::post('/product/{product}/purchase', [MarketplaceController::class, 'purchase'])->name('purchase');
        Route::get('/purchased', [MarketplaceController::class, 'purchased'])->name('purchased');
        Route::get('/sold', [MarketplaceController::class, 'sold'])->name('sold');
        Route::get('/order/{order}', [MarketplaceController::class, 'orderDetail'])->name('order.detail');
    });
    
    // Product inventory routes
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/update-quantity', [ProductController::class, 'updateQuantity'])->name('products.update-quantity');
    
    // Token management
    Route::get('/tokens', [UserController::class, 'tokens'])->name('tokens.index');
    Route::post('/tokens', [UserController::class, 'createToken'])->name('tokens.create');
    Route::delete('/tokens/{token}', [UserController::class, 'destroyToken'])->name('tokens.destroy');
    
    // Route for sending email
    Route::post('/users/{id}/send-email', [UserController::class, 'sendEmail'])->name('users.send-email');
    
    // Route for sending telegram message
    Route::post('/users/{id}/send-telegram', [UserController::class, 'sendTelegram'])->name('users.send-telegram');
    
    // Event Management routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [App\Http\Controllers\EventController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\EventController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\EventController::class, 'store'])->name('store');
        Route::get('/{event}', [App\Http\Controllers\EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [App\Http\Controllers\EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [App\Http\Controllers\EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [App\Http\Controllers\EventController::class, 'destroy'])->name('destroy');
        
        // Event registration routes
        Route::post('/{event}/register', [\App\Http\Controllers\EventRegistrationController::class, 'register'])->name('register');
        Route::get('/registrations/my', [\App\Http\Controllers\EventController::class, 'myRegistrations'])->name('registrations.my');
        Route::delete('/registrations/{registration}/cancel', [\App\Http\Controllers\EventRegistrationController::class, 'cancel'])->name('registration.cancel');
        
        // Event organizer routes
        Route::get('/organized/list', [\App\Http\Controllers\EventController::class, 'organized'])->name('organized');
        Route::get('/{event}/attendees', [\App\Http\Controllers\EventController::class, 'attendees'])->name('attendees');
        Route::post('/registrations/{registration}/check-in', [\App\Http\Controllers\EventRegistrationController::class, 'checkIn'])->name('registration.check-in');
    });
    
    // Event Category routes (Admin only except index and show)
    Route::resource('event-categories', App\Http\Controllers\EventCategoryController::class);
    Route::get('/event-categories/{category}/events', [App\Http\Controllers\EventCategoryController::class, 'events'])->name('event-categories.events');
    Route::get('/event-categories/{category}/events/create', [App\Http\Controllers\EventCategoryController::class, 'createEvent'])->name('event-categories.events.create');
    Route::post('/event-categories/{category}/events', [App\Http\Controllers\EventCategoryController::class, 'storeEvent'])->name('event-categories.events.store');
    Route::get('/event-categories/{category}/events/{event}', [App\Http\Controllers\EventCategoryController::class, 'showEvent'])->name('event-categories.events.show');
    Route::get('/event-categories/{category}/events/{event}/edit', [App\Http\Controllers\EventCategoryController::class, 'editEvent'])->name('event-categories.events.edit');
    Route::put('/event-categories/{category}/events/{event}', [App\Http\Controllers\EventCategoryController::class, 'updateEvent'])->name('event-categories.events.update');
    Route::delete('/event-categories/{category}/events/{event}', [App\Http\Controllers\EventCategoryController::class, 'destroyEvent'])->name('event-categories.events.destroy');
    
    
    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{userId}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/messages', [ChatController::class, 'store'])->name('chat.messages.store');
    Route::post('/chat/messages/{id}/read', [ChatController::class, 'markAsRead'])->name('chat.messages.read');
    Route::post('/chat/messages/{userId}/read-all', [ChatController::class, 'markAllAsRead'])->name('chat.messages.read-all');
});
Route::get('/print-job/{message}', function ($message)  {

    
    \App\Jobs\PrintToConsoleJob::dispatch($message);
    return 'Job untuk mencetak pesan ke konsol telah di-dispatch dengan pesan: ' . $message;
})->name('print.job');
Broadcast::routes(['middleware' => ['auth']]);

