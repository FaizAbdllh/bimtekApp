<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bimtek;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

        $inputToken = trim($request->token);

        // 💡 SINKRONISASI DB: Pencocokan langsung ke kolom 'token_hash'
        $user = User::where('email', $request->email)
            ->where('token_hash', $inputToken)
            ->where('is_active', 0) // Pastikan akun memang belum aktif
            ->first();

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Token aktivasi tidak valid atau email salah.');
        }

        // 💡 SINKRONISASI DB: Validasi Kedaluwarsa Waktu menggunakan kolom asli 'expires_at'
        if ($user->expires_at && now()->gt($user->expires_at)) {
            return redirect()->back()->withInput()->with('error', 'Token aktivasi ini sudah kedaluwarsa. Silakan hubungi panitia untuk token baru.');
        }

        // 3. JIKA VALID: Oper status ke Blade Tahap 2 menggunakan Flash Session
        return redirect()->back()->with([
            'token_verified' => true,
            'verified_email' => $request->email,
            'verified_token' => $inputToken 
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

        $inputToken = trim($request->token);

        // Verifikasi ulang user dan token langsung di tabel users (Security Layer)
        $user = User::where('email', $request->email)
            ->where('token_hash', $inputToken)
            ->where('is_active', 0)
            ->first();
        
        if (! $user) {
            return redirect()->to('/aktivasi')->with('error', 'Sesi aktivasi tidak valid atau sudah digunakan, silakan ulangi.');
        }

        // Double Check Kedaluwarsa di Tahap Akhir
        if ($user->expires_at && now()->gt($user->expires_at)) {
            return redirect()->to('/aktivasi')->with('error', 'Proses gagal. Token telah kedaluwarsa.');
        }

        // 💡 SINKRONISASI DB: Menggunakan token_hash, expires_at, dan used_at sekaligus
        $user->update([
            'password'   => Hash::make($request->password),
            'is_active'  => 1,
            'token_hash' => null, // Dikosongkan agar token hangus & tidak bisa disalahgunakan
            'expires_at' => null,
            'used_at'    => now(),
        ]);

        // 2. Otomatis Login-kan peserta ke dalam sistem
        Auth::login($user);

        // =====================================================================
        // 💡 PERBAIKAN UTAMA: Mengalihkan pencarian ke tabel baru 'bimtek_pesertas'
        // =====================================================================
        $assignedBimtek = DB::table('bimtek_pesertas')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        if ($assignedBimtek) {
            // Jika hubungan kelas ditemukan, arahkan langsung ke halaman upload persyaratan berkas
            return redirect()->route('bimtek.verifikasi-dokumen.upload-form', $assignedBimtek->bimtek_id)
                ->with('success', 'Akun Anda berhasil diaktifkan! Silakan unggah dokumen persyaratan Anda.');
        }

        // Fallback jika karena suatu hal data jembatan tidak terbaca, arahkan ke dashboard utama
        return redirect()->route('dashboard')
            ->with('success', 'Akun Anda berhasil diaktifkan!');
    }

    /**
     * Menampilkan form registrasi mandiri via link kode undangan
     */
    public function showRegistrationForm(Request $request)
    {
        $inviteCode = $request->query('code');

        // Cari kelas bimtek yang memiliki kode undangan tersebut
        $bimtek = Bimtek::where('invite_code', $inviteCode)->first();

        // Jika kode tidak diisi atau kelas tidak ditemukan, lempar 404 khusus
        if (!$inviteCode || !$bimtek) {
            return abort(404, 'Link pendaftaran tidak valid atau kelas Bimtek telah ditutup.');
        }

        return view('auth.register', compact('bimtek', 'inviteCode'));
    }

    /**
     * Memproses submit data akun dari peserta luar
     */
    public function submitRegistration(Request $request)
    {
        $request->validate([
            'invite_code'   => 'required|string',
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'nip'           => 'nullable|string|max:50|unique:users,nip',
            'asal_instansi' => 'nullable|string|max:255',
        ]);

        $bimtek = Bimtek::where('invite_code', $request->invite_code)->first();
        if (!$bimtek) {
            return back()->withInput()->with('error', 'Kode undangan kadaluwarsa atau tidak sah.');
        }

        // =====================================================================
        // 💡 PERBAIKAN DEFINITIF: Ambil UUID Role secara dinamis dari tabel roles
        // =====================================================================
        $rolePeserta = Role::where('nama_peran', 'Peserta Eksternal')
            ->orWhere('nama_peran', 'Peserta')
            ->first();

        if (!$rolePeserta) {
            return back()->withInput()->with('error', 'Konfigurasi Role untuk Peserta tidak ditemukan di database.');
        }

        // 1. Buat Akun User Baru langsung dengan status Aktif (karena mendaftar lewat link resmi)
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'nip'           => $request->nip,
            'asal_instansi' => $request->asal_instansi,
            'role_id'       => $rolePeserta->id, // Menggunakan UUID string asli hasil query di atas
            'is_active'     => 1,
            'used_at'       => now(), // Sesuai kolom database riil
        ]);

        // 2. Pasangkan secara otomatis ke dalam tabel jembatan keanggotaan kelas
        $bimtek->peserta()->attach($user->id, [
            'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'invited' : 'verified',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 3. Otomatis login-kan user ke sistem
        Auth::login($user);

        // 4. Arahkan ke form upload berkas jika butuh verifikasi, atau ke dashboard jika bebas syarat
        if ($bimtek->butuh_verifikasi_dokumen) {
            return redirect()->route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id)
                ->with('success', 'Registrasi berhasil! Silakan lengkapi berkas persyaratan administrasi Anda.');
        }

        return redirect()->route('dashboard')->with('success', 'Selamat bergabung! Akun Anda berhasil diaktifkan.');
    }
}