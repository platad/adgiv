<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected AuthService $authService;

    /**
     * AuthController constructor.
     *
     * @param  \App\Services\AuthService  $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember');

        if ($this->authService->attemptLogin($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // Translate login validation error dynamically based on the current locale
        $locale = App::getLocale();
        $errorMsg = 'Email atau password salah.';
        
        if ($locale === 'en') {
            $errorMsg = 'Invalid email or password.';
        } elseif ($locale === 'zh') {
            $errorMsg = '邮箱或密码错误。';
        }

        return back()
            ->withErrors(['email' => $errorMsg])
            ->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to root "/" to let the locale detection route correctly determine the page language
        return redirect('/');
    }

    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        $user = $this->authService->registerUser($data);
        
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
