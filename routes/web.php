<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes – BIMA Multi-Agent AI Voice Analysis (Multi-Modal v3)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ── Authentication ──────────────────────────────────────────────

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (Request $req) {
    $credentials = $req->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);
    if (Auth::attempt($credentials, $req->boolean('remember'))) {
        $req->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
})->middleware('guest');

Route::post('/logout', function (Request $req) {
    Auth::logout();
    $req->session()->invalidate();
    $req->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/register', function (Request $req) {
    $validated = $req->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
    ]);
    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);
    Auth::login($user);
    return redirect()->route('dashboard');
})->middleware('guest');

// ── App Routes (Auth Protected) ─────────────────────────────────

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/realtime-data', [DashboardController::class, 'getRealtimeChartData'])->name('dashboard.realtime-data');
    Route::get('/dashboard/history-data', [DashboardController::class, 'getHistoryData'])->name('dashboard.history-data');

    // Analysis Workflow
    Route::prefix('analysis')->name('analysis.')->group(function () {
        Route::get('/create', [AnalysisController::class, 'create'])->name('create'); // Input voice
        Route::post('/store', [AnalysisController::class, 'store'])->name('store');   // Save audio, create Analysis record
        Route::get('/{id}/processing', [AnalysisController::class, 'processing'])->name('processing'); // Step-by-step view
        Route::post('/{id}/process', [AnalysisController::class, 'processAudio'])->name('process'); // Trigger AI Multi-Modal
        Route::get('/{id}/result', [AnalysisController::class, 'result'])->name('result'); // Final annotated view
        Route::post('/{id}/feedback', [AnalysisController::class, 'feedback'])->name('feedback'); // Submit Kesesuaian
        Route::post('/{id}/line-feedback', [AnalysisController::class, 'lineFeedback'])->name('line-feedback'); // Submit Line-by-line Feedback
        Route::delete('/{id}', [AnalysisController::class, 'destroy'])->name('destroy'); // Delete Analysis
    });
});
