<?php

use Illuminate\Support\Facades\Route;
use Modules\Forms\app\Http\Controllers\FormController;
use Modules\Forms\app\Http\Controllers\FormQuestionController;
use Modules\Forms\app\Http\Controllers\FormResponseController;

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

Route::prefix('forms')->middleware(['web'])->group(function () {
    // Public routes
    Route::get('/', function() {
        return redirect()->route('forms.index');
    });
    
    // Form response routes (public access)
    Route::get('f/{slug}', [FormResponseController::class, 'create'])->name('forms.public');
    Route::post('f/{slug}/submit', [FormResponseController::class, 'store'])->name('forms.responses.store');
    Route::get('f/{slug}/thank-you', [FormResponseController::class, 'thankYou'])->name('forms.responses.thank-you');
});

// Protected routes
Route::prefix('forms')->middleware(['web', 'auth'])->group(function () {
    // Form management routes
    Route::get('/dashboard', [FormController::class, 'index'])->name('forms.index');
    Route::get('/create', [FormController::class, 'create'])->name('forms.create');
    Route::post('/', [FormController::class, 'store'])->name('forms.store');
    Route::get('/{form}', [FormController::class, 'show'])->name('forms.show');
    Route::get('/{form}/edit', [FormController::class, 'edit'])->name('forms.edit');
    Route::put('/{form}', [FormController::class, 'update'])->name('forms.update');
    Route::delete('/{form}', [FormController::class, 'destroy'])->name('forms.destroy');
    
    // Form question routes
    Route::get('/{form}/questions', [FormQuestionController::class, 'index'])->name('forms.questions.index');
    Route::get('/{form}/questions/create', [FormQuestionController::class, 'create'])->name('forms.questions.create');
    Route::post('/{form}/questions', [FormQuestionController::class, 'store'])->name('forms.questions.store');
    Route::get('/{form}/questions/{question}/edit', [FormQuestionController::class, 'edit'])->name('forms.questions.edit');
    Route::put('/{form}/questions/{question}', [FormQuestionController::class, 'update'])->name('forms.questions.update');
    Route::delete('/{form}/questions/{question}', [FormQuestionController::class, 'destroy'])->name('forms.questions.destroy');
    Route::post('/{form}/questions/order', [FormQuestionController::class, 'updateOrder'])->name('forms.questions.order');
    
    // Form response management routes
    Route::get('/{form}/responses', [FormResponseController::class, 'index'])->name('forms.responses.index');
    Route::get('/{form}/responses/{response}', [FormResponseController::class, 'show'])->name('forms.responses.show');
    Route::get('/{form}/responses/export', [FormResponseController::class, 'export'])->name('forms.responses.export');
});
