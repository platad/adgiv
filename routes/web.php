<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::pattern('locale', 'id|en|zh');   

Route::get('/', function (Request $request) {
    $locale = $request->cookie('locale');
    
    if (!$locale || !in_array($locale, ['id', 'en', 'zh'])) {
        $locale = $request->getPreferredLanguage(['id', 'en', 'zh']) ?: 'id';
    }

    $target = auth()->check() ? 'dashboard' : 'login';
    return redirect()->route($target, ['locale' => $locale]);
});

Route::prefix('{locale}')->middleware([\App\Http\Middleware\Localization::class])->group(function () {

    Route::get('/', function () {
        $target = auth()->check() ? 'dashboard' : 'login';
        return redirect()->route($target);
    });

    Route::get('/privacy-consent', function () {
        return view('auth.privacy-consent');
    })->name('privacy.consent');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
    Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

    Route::middleware(['auth'])->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/realtime-data', [DashboardController::class, 'getRealtimeChartData'])->name('dashboard.realtime-data');
        Route::get('/dashboard/history-data', [DashboardController::class, 'getHistoryData'])->name('dashboard.history-data');
        Route::get('/methodology', [DashboardController::class, 'methodology'])->name('methodology');

        Route::prefix('analysis')->name('analysis.')->group(function () {
            Route::get('/create', [AnalysisController::class, 'create'])->name('create');
            Route::post('/initialize', [AnalysisController::class, 'initialize'])->name('initialize');

            Route::get('/{analysis}/processing', [AnalysisController::class, 'processing'])->name('processing');
            Route::post('/{analysis}/saveResult', [AnalysisController::class, 'saveResult'])->name('saveResult');
            Route::get('/{analysis}/audio', [AnalysisController::class, 'getAudio'])->name('audio');
            Route::get('/{analysis}/result', [AnalysisController::class, 'result'])->name('result');
            Route::get('/{analysis}/print', [AnalysisController::class, 'printReport'])->name('print');
            Route::post('/{analysis}/feedback', [AnalysisController::class, 'feedback'])->name('feedback');
            Route::post('/{analysis}/line-feedback', [AnalysisController::class, 'lineFeedback'])->name('line-feedback');
            Route::delete('/{analysis}', [AnalysisController::class, 'destroy'])->name('destroy');
        });
    });
});
