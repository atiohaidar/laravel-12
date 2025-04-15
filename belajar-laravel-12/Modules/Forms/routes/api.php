<?php

use Illuminate\Support\Facades\Route;
use Modules\Forms\Http\Controllers\FormsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('forms', FormsController::class)->names('forms');
});
