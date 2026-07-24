<?php

use App\Http\Controllers\AnalysisController;
use Illuminate\Support\Facades\Route;

Route::post('/api/webhook/{slug}', [AnalysisController::class, 'webhookResult'])
    ->name('api.webhook');
