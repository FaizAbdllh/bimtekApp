<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Redirect berdasarkan role user
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Log aktivitas login
        \App\Models\LogSistem::info('Login berhasil', auth()->id());

        // Redirect berdasarkan role
        return $this->redirectBasedOnRole();
    }

    /**
     * Redirect user berdasarkan role mereka
     */
    protected function redirectBasedOnRole(): RedirectResponse
    {
        $user = Auth::user();
        
        // Semua role redirect ke dashboard yang sama
        // Nanti di dashboard akan ditampilkan konten berbeda sesuai role
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
