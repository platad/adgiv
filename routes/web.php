<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes – BIMA Multi-Agent AI Debate System
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('chat.index')
        : redirect()->route('login');
});

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
        return redirect()->intended(route('chat.index'));
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
    return redirect()->route('chat.index');
})->middleware('guest');


Route::middleware(['auth'])->prefix('chat')->name('chat.')->group(function () {

    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/sessions', [ChatController::class, 'listSessions'])->name('sessions.list');

    Route::post('/session', [ChatController::class, 'createSession'])->name('session.create');
    Route::get('/session/{session}/messages', [ChatController::class, 'sessionMessages'])->name('session.messages');
    Route::get('/session/{id}/data', [ChatController::class, 'getSessionData']);

    Route::post('/analyse/step-1', [ChatController::class, 'analyseStep1'])->name('analyse.step1');
    Route::post('/analyse/step-2', [ChatController::class, 'analyseStep2'])->name('analyse.step2');
    Route::post('/analyse/step-3', [ChatController::class, 'analyseStep3'])->name('analyse.step3');
    Route::post('/analyse/step-4', [ChatController::class, 'analyseStep4'])->name('analyse.step4');
    Route::post('/analyse/step-5', [ChatController::class, 'analyseStep5'])->name('analyse.step5');
    Route::post('/analyse/step-6', [ChatController::class, 'analyseStep6'])->name('analyse.step6');

    Route::post('/transcribe', [ChatController::class, 'transcribeAudio'])->name('transcribe');

    Route::delete('/session/{session}', [ChatController::class, 'deleteSession'])->name('session.delete');
});
