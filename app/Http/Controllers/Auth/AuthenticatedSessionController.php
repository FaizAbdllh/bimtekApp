<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Bimtek;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        if ($request->filled('invite_code')) {
            session(['pending_invite_code' => $request->query('invite_code')]);
        }

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

        $inviteCode = $request->session()->pull('pending_invite_code');

        if ($inviteCode) {
            $bimtek = Bimtek::where('invite_code', $inviteCode)->first();

            if ($bimtek && $bimtek->status === 'registrasi') {
                $user = Auth::user();

                $isParticipant = DB::table('bimtek_pesertas')
                    ->where('bimtek_id', $bimtek->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (! $isParticipant) {
                    DB::table('bimtek_pesertas')->insert([
                        'bimtek_id' => $bimtek->id,
                        'user_id' => $user->id,
                        'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen
                            ? 'invited'
                            : 'verified',
                        'notified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $status = DB::table('bimtek_pesertas')
                    ->where('bimtek_id', $bimtek->id)
                    ->where('user_id', $user->id)
                    ->value('status_verifikasi');

                if ($bimtek->butuh_verifikasi_dokumen && $status !== 'verified') {
                    return redirect()
                        ->route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id)
                        ->with('success', 'Akun berhasil dihubungkan ke bimtek.');
                }

                return redirect()->route('bimtek.show', $bimtek->id);
            }
        }

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
