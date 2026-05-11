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

    Route::post('/session', [ChatController::class, 'createSession'])->name('session.create');
    Route::get('/sessions', [ChatController::class, 'sessions'])->name('sessions');
    Route::get('/session/{session}/messages', [ChatController::class, 'sessionMessages'])
        ->name('session.messages');

    Route::post('/analyse', [ChatController::class, 'analyse'])->name('analyse');

    Route::post('/transcribe', [ChatController::class, 'transcribeAudio'])->name('transcribe');

    Route::post('/upload-document', [ChatController::class, 'uploadDocument'])->name('upload-document');

    Route::delete('/session/{session}', [ChatController::class, 'deleteSession'])->name('session.delete');
});

// ── Web-Triggered Queue Worker (For cPanel/Shared Hosting) ────────────────
Route::get('/queue/work', function (Request $request) {
    // Security check: Match token from .env or fallback to secret string
    $token = config('app.queue_token', 'bima-secret-123');
    
    if ($request->get('token') !== $token) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    try {
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 3,
            '--backoff' => 3
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Queue processed.',
            'output' => Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
