<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bimtek;
use App\Models\Role;
use App\Models\DokumenPersyaratanPeserta; // 💡 WAJIB DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivationController extends Controller
{
    /**
     * TAHAP PENDAFTARAN: Menampilkan form registrasi mandiri via link kode undangan
     */
    public function showRegistrationForm(Request $request)
    {
        $inviteCode = $request->query('code');
        $bimtek = Bimtek::where('invite_code', $inviteCode)->first();

        if (!$inviteCode || !$bimtek) {
            return abort(404, 'Link pendaftaran tidak valid atau kelas Bimtek telah ditutup.');
        }

        // ALUMNI DETECTED: Jika user sudah login, jangan suruh isi form lagi.
        if (Auth::check()) {
            return view('auth.register-join-confirm', compact('bimtek', 'inviteCode'));
        }

        // Simpan kode undangan ke session untuk cadangan jika nanti dia dialihkan ke halaman login
        session(['pending_invite_code' => $inviteCode]);

        return view('auth.register', compact('bimtek', 'inviteCode'));
    }

    /**
     * TAHAP PENDAFTARAN: Memproses submit data akun + berkas dari peserta luar
     */
    public function submitRegistration(Request $request)
    {
        // 1. Validasi awal input form (Sudah termasuk deteksi file dokumen)
        $request->validate([
            'invite_code'   => 'required',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email',
            'nip'           => 'required|string',
            'asal_instansi' => 'required|string',
            'dokumen'       => 'nullable|array', 
            'dokumen.*'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Maks 5MB per file
        ]);

        $bimtek = Bimtek::where('invite_code', $request->invite_code)->firstOrFail();

        // 2. Cek apakah user sudah terdaftar berdasarkan EMAIL atau NIP
        $existingUser = User::where('email', $request->email)
            ->orWhere('nip', $request->nip)
            ->first();

        $registeredUserId = null;

        if ($existingUser) {
            // Cek apakah user tersebut sudah terdaftar di Bimtek ini
            $isAlreadyJoined = $bimtek->peserta()->where('user_id', $existingUser->id)->exists();

            if ($isAlreadyJoined) {
                return redirect()->route('login')
                    ->with('info', 'NIP atau Email Anda sudah terdaftar di kegiatan Bimtek ini. Silakan login ke akun Anda.');
            }

            // Jika user sudah punya akun di platform tetapi belum masuk ke Bimtek ini, daftarkan langsung
            $bimtek->peserta()->attach($existingUser->id, [
                'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'diverifikasi',
            ]);

            $registeredUserId = $existingUser->id;
        } else {
            // 3. Jika user benar-benar baru, buat entitas user baru
            $rolePeserta = Role::where('nama_peran', 'Peserta Eksternal')
                ->orWhere('nama_peran', 'Peserta')
                ->first();

            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make(Str::random(32)),
                'nip'           => $request->nip,
                'asal_instansi' => $request->asal_instansi,
                'role_id'       => $rolePeserta?->id,
                'is_active'     => 0,
            ]);

            // Hubungkan user baru ke tabel bridge peserta Bimtek
            $bimtek->peserta()->attach($user->id, [
                'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'diverifikasi',
            ]);

            $registeredUserId = $user->id;
        }

        // 💡 4. PROSES TANGKAP DAN SIMPAN BERKAS PERSYARATAN
        if ($bimtek->butuh_verifikasi_dokumen && $request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $syaratId => $file) {
                if ($file && $file->isValid()) {
                    // Simpan file fisik ke folder storage/app/public/dokumen-persyaratan/...
                    $path = $file->store("dokumen-persyaratan/{$bimtek->id}", 'public');

                    // Simpan record data berkas ke tabel dokumen_persyaratan_peserta
                    DokumenPersyaratanPeserta::updateOrCreate(
                        [
                            'bimtek_id'         => $bimtek->id,
                            'user_id'           => $registeredUserId,
                            'syarat_dokumen_id' => $syaratId,
                        ],
                        [
                            'file_path'   => $path,
                            'file_name'   => $file->getClientOriginalName(), // 💡 WAJIB ADA: Mengambil nama asli file (misal: surat_tugas.pdf)
                            'status'      => 'pending',
                            'uploaded_at' => now(), // 💡 WAJIB ADA: Menyimpan timestamp waktu unggah
                        ]
                    );
                }
            }
        }

        $message = $existingUser 
            ? 'NIP/Email Anda sudah terdaftar di platform BBPMP. Pendaftaran kelas berhasil dan berkas Anda sedang antre untuk diperiksa. Silakan login!' 
            : 'Pendaftaran berhasil! Berkas administrasi Anda telah diterima dan sedang dalam antrean pemeriksaan panitia. Silakan login.';

        return redirect()->route('login')->with('success', $message);
    }

    /**
     * TAHAP AKTIVASI (MAGIC LINK): Membuka link dari email / WA secara langsung
     * URL Pattern: /aktivasi/{token}
     */
    public function processMagicLink($token)
    {
        // Sistem langsung mencari pemilik token di balik layar (Tanpa ketik manual)
        $user = User::where('token_hash', $token)
            ->where('is_active', 0)
            ->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Tautan aktivasi tidak valid atau sudah pernah digunakan.');
        }

        // Validasi Kedaluwarsa Waktu token
        if ($user->expires_at && now()->gt($user->expires_at)) {
            return redirect()->route('login')->with('error', 'Tautan aktivasi ini sudah kedaluwarsa. Silakan hubungi panitia BBPMP.');
        }

        // Jika lolos semua validasi, langsung buka halaman set password baru
        return view('auth.set-password', compact('user', 'token'));
    }

    /**
     * TAHAP AKTIVASI: Eksekusi simpan password buatan peserta dan aktifkan akun
     */
    public function activateAccountAndSetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('token_hash', $request->token)
            ->where('is_active', 0)
            ->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesi aktivasi tidak valid.');
        }

        // Eksekusi Aktivasi Akun secara permanen
        $user->update([
            'password'   => Hash::make($request->password),
            'is_active'  => 1, // 🔓 AKUN RESMI AKTIF!
            'token_hash' => null, // Hanguskan token agar tidak bisa diklik ulang
            'expires_at' => null,
            'used_at'    => now(),
        ]);

        // Otomatis login-kan peserta ke dalam sistem
        Auth::login($user);

        // Cari tahu kelas bimektnya untuk diarahkan ke halaman yang sesuai
        $assignedBimtek = DB::table('bimtek_pesertas')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        if ($assignedBimtek) {
            return redirect()->route('dashboard')
                ->with('success', 'Selamat! Akun Anda berhasil diaktifkan dan terdaftar resmi sebagai peserta.');
        }

        return redirect()->route('dashboard')->with('success', 'Akun Anda berhasil diaktifkan!');
    }
}