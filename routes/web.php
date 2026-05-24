<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes – BIMA Multi-Agent AI Voice Analysis (Multi-Modal v3)
|--------------------------------------------------------------------------
*/

// Explicitly bind the locale route parameter to only match ID, EN, or ZH
Route::pattern('locale', 'id|en|zh');

// Root Dynamic Redirection based on Cookie or network Accept-Language header
Route::get('/', function (Request $request) {
    // 1. Detect from cookie (saved in browser cache)
    $locale = $request->cookie('locale');
    
    // 2. Fallback to browser's Accept-Language header
    if (!$locale || !in_array($locale, ['id', 'en', 'zh'])) {
        $locale = $request->getPreferredLanguage(['id', 'en', 'zh']) ?: 'id';
    }

    $target = auth()->check() ? 'dashboard' : 'login';
    return redirect()->route($target, ['locale' => $locale]);
});

// Group all localized routes under {locale} prefix
Route::prefix('{locale}')->middleware([\App\Http\Middleware\Localization::class])->group(function () {

    Route::get('/', function () {
        $target = auth()->check() ? 'dashboard' : 'login';
        return redirect()->route($target);
    });

    Route::get('/privacy-consent', function () {
        return view('auth.privacy-consent');
    })->name('privacy.consent');

    // ── Authentication ──────────────────────────────────────────────

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

    // ── App Routes (Auth Protected) ─────────────────────────────────

    Route::middleware(['auth'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/realtime-data', [DashboardController::class, 'getRealtimeChartData'])->name('dashboard.realtime-data');
        Route::get('/dashboard/history-data', [DashboardController::class, 'getHistoryData'])->name('dashboard.history-data');
        Route::get('/methodology', [DashboardController::class, 'methodology'])->name('methodology');

        // Analysis Workflow
        Route::prefix('analysis')->name('analysis.')->group(function () {
            Route::get('/create', [AnalysisController::class, 'create'])->name('create'); // Input voice
            Route::post('/initialize', [AnalysisController::class, 'initialize'])->name('initialize'); // Initialize Analysis record
            Route::post('/{id}/chunk', [AnalysisController::class, 'storeChunk'])->name('chunk'); // Upload & analyze an audio chunk
            Route::post('/store', [AnalysisController::class, 'store'])->name('store');   // Save audio, create Analysis record
            Route::get('/{id}/processing', [AnalysisController::class, 'processing'])->name('processing'); // Step-by-step view
            Route::post('/{id}/process', [AnalysisController::class, 'processAudio'])->name('process'); // Trigger AI Multi-Modal Synthesis
            Route::get('/{id}/result', [AnalysisController::class, 'result'])->name('result'); // Final annotated view
            Route::get('/{id}/print', [AnalysisController::class, 'printReport'])->name('print'); // Print-friendly clean report view
            Route::post('/{id}/feedback', [AnalysisController::class, 'feedback'])->name('feedback'); // Submit Kesesuaian
            Route::post('/{id}/line-feedback', [AnalysisController::class, 'lineFeedback'])->name('line-feedback'); // Submit Line-by-line Feedback
            Route::delete('/{id}', [AnalysisController::class, 'destroy'])->name('destroy'); // Delete Analysis
        });
    });
});
