<?php

use App\Http\Controllers\AnalysisController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/{analysis:slug}', [AnalysisController::class, 'webhookResult'])
    ->name('api.webhook');
