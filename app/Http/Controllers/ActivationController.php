<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ActivationController extends Controller
{
    /**
     * Tampilkan halaman form aktivasi (Tahap 1)
     */
    public function showManualActivationForm(Request $request)
    {
        // 1. Tangkap data email dan token dari query string URL email
        $email = $request->query('email');
        $token = $request->query('token');

        // 2. Oper variabel tersebut ke dalam file Blade activation.manual
        return view('activation.manual', compact('email', 'token')); 
    }

    /**
     * TAHAP 1: Verifikasi Email dan Token Manual via POST
     */
    public function verifyManualToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        // 1. Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Email tidak terdaftar di sistem.');
        }

        // =====================================================================
        // FIX DEFINITIF: Konversi Token Mentah ke SHA-256 Sebelum Kueri ke DB
        // =====================================================================
        $inputToken = trim($request->token);
        $tokenHash = hash('sha256', $inputToken);

        // Cari token di database yang cocok dengan hasil hash SHA-256
        $tokenRecord = ActivationToken::where('user_id', $user->id)
            ->where('token_hash', $tokenHash) // <-- Menggunakan hasil hash
            ->whereNull('used_at')
            ->whereNull('revoked_at')
            ->first();

        if (! $tokenRecord) {
            return redirect()->back()->withInput()->with('error', 'Token aktivasi tidak valid atau sudah digunakan.');
        }

        // Validasi Kedaluwarsa Waktu Token
        $expirationField = isset($tokenRecord->expires_at) ? 'expires_at' : 'expired_at';
        if (isset($tokenRecord->$expirationField) && now()->gt($tokenRecord->$expirationField)) {
            return redirect()->back()->withInput()->with('error', 'Token aktivasi ini sudah kedaluwarsa. Silakan hubungi panitia untuk token baru.');
        }

        // 3. JIKA VALID: Oper status ke Blade Tahap 2 menggunakan Flash Session
        return redirect()->back()->with([
            'token_verified' => true,
            'verified_email' => $request->email,
            'verified_token' => $inputToken // Kirimkan token mentah untuk Tahap 2 nanti
        ]);
    }

    /**
     * TAHAP 2: Eksekusi Pembuatan Password Baru & Aktivasi Akun
     */
   public function setPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (! $user) {
            return redirect()->to('/aktivasi')->with('error', 'Sesi aktivasi tidak valid, email tidak ditemukan.');
        }

        // Konversi kembali token mentah ke SHA-256 untuk verifikasi akhir
        $inputToken = trim($request->token);
        $tokenHash = hash('sha256', $inputToken);

        $tokenRecord = ActivationToken::where('user_id', $user->id)
            ->where('token_hash', $tokenHash) // <-- Menggunakan hasil hash
            ->whereNull('used_at')
            ->whereNull('revoked_at')
            ->first();

        if (! $tokenRecord) {
            return redirect()->to('/aktivasi')->with('error', 'Sesi aktivasi kedaluwarsa atau tidak valid, silakan ulangi.');
        }

        // Double Check Kedaluwarsa di Tahap Akhir (Security Layer)
        $expirationField = isset($tokenRecord->expires_at) ? 'expires_at' : 'expired_at';
        if (isset($tokenRecord->$expirationField) && now()->gt($tokenRecord->$expirationField)) {
            return redirect()->to('/aktivasi')->with('error', 'Proses gagal. Token telah kedaluwarsa.');
        }

        // 1. Update Password User
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // 2. Tandai token telah sukses digunakan
        $tokenRecord->update([
            'used_at' => now()
        ]);

        // 3. Otomatis Login-kan peserta ke dalam sistem
        Auth::login($user);

        // =====================================================================
        // FIX: Ambil bimtek_id dari pivot table karena token tidak menyimpan bimtek_id
        // =====================================================================
        $assignedBimtek = \Illuminate\Support\Facades\DB::table('bimtek_user')
            ->where('user_id', $user->id)
            ->where('peran_kontekstual', 'peserta')
            ->where('status_verifikasi', 'invited')
            ->latest('notified_at')
            ->first();

        if ($assignedBimtek) {
            // Jika data pivot hubungan bimtek ditemukan, arahkan langsung ke halaman upload
            return redirect()->route('bimtek.verifikasi-dokumen.upload-form', $assignedBimtek->bimtek_id)
                ->with('success', 'Akun Anda berhasil diaktifkan! Silakan unggah dokumen persyaratan Anda.');
        }

        // Fallback jika karena suatu hal hubungan bimtek tidak terbaca, arahkan ke dashboard utama
        return redirect()->route('dashboard')
            ->with('success', 'Akun Anda berhasil diaktifkan!');
    }
}