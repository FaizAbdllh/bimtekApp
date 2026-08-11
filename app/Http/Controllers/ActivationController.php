<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bimtek;
use App\Models\Role;
use App\Models\DokumenPersyaratanPeserta;
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
        // 1. Validasi awal input form
        $request->validate([
            'invite_code'   => 'required',
            'name'          => 'required|string|max:255',
            'email'         => 'required|email',
            'nip'           => 'required|string',
            'asal_instansi' => 'required|string',
            'password'      => 'required|string|min:8|confirmed',
            'dokumen'       => 'nullable|array', 
            'dokumen.*'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $bimtek = Bimtek::where('invite_code', $request->invite_code)->firstOrFail();

        // Gunakan DB Transaction untuk menjaga konsistensi data antara tabel users dan bimtek_pesertas
        return DB::transaction(function () use ($request, $bimtek) {
            
            // Cek apakah user sudah terdaftar di Bimtek ini secara spesifik
            // Kita cek berdasarkan relasi ke tabel bimtek_pesertas terlebih dahulu
            $existingInBimtek = DB::table('bimtek_pesertas')
                ->join('users', 'bimtek_pesertas.user_id', '=', 'users.id')
                ->where('bimtek_pesertas.bimtek_id', $bimtek->id)
                ->where(function ($query) use ($request) {
                    $query->where('users.email', $request->email)
                        ->orWhere('users.nip', $request->nip);
                })
                ->exists();

            if ($existingInBimtek) {
                return redirect()->route('login')
                    ->with('info', 'NIP atau Email Anda sudah terdaftar di kegiatan Bimtek ini. Silakan login ke akun Anda.');
            }

            // Cari apakah akun usernya sudah ada secara global di platform
            $user = User::where('email', $request->email)
                ->orWhere('nip', $request->nip)
                ->first();

            $isNewUser = false;

            if ($user) {
                // Jika user sudah ada (misal diinput admin sebelumnya), perbarui informasi dan password-nya
                // agar peserta bisa login menggunakan password yang baru saja mereka daftarkan.
                $user->update([
                    'name'          => $request->name,
                    'nip'           => $request->nip,
                    'asal_instansi' => $request->asal_instansi,
                    'password'      => Hash::make($request->password),
                    'is_active'     => 1,
                ]);
            } else {
                // Jika benar-benar baru, buat akun baru
                $rolePeserta = Role::where('nama_peran', 'Peserta Eksternal')
                    ->orWhere('nama_peran', 'Peserta')
                    ->first();

                $user = User::create([
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'password'      => Hash::make($request->password),
                    'nip'           => $request->nip,
                    'asal_instansi' => $request->asal_instansi,
                    'role_id'       => $rolePeserta?->id,
                    'is_active'     => 1, 
                ]);

                $isNewUser = true;
            }

            // Hubungkan user ke tabel bridge peserta Bimtek (menggunakan Composite PK: bimtek_id & user_id)
            // Kita gunakan updateOrInsert agar aman dari duplikasi baris
            DB::table('bimtek_pesertas')->updateOrInsert(
                [
                    'bimtek_id' => $bimtek->id,
                    'user_id'   => $user->id,
                ],
                [
                    'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'verified',
                    'updated_at'        => now(),
                    'created_at'        => now(),
                ]
            );

            // 4. PROSES TANGKAP DAN SIMPAN BERKAS PERSYARATAN
            if ($bimtek->butuh_verifikasi_dokumen && $request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $syaratId => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store("dokumen-persyaratan/{$bimtek->id}", 'public');

                        DokumenPersyaratanPeserta::updateOrCreate(
                            [
                                'bimtek_id'         => $bimtek->id,
                                'user_id'           => $user->id,
                                'syarat_dokumen_id' => $syaratId,
                            ],
                            [
                                'file_path'   => $path,
                                'file_name'   => $file->getClientOriginalName(),
                                'status'      => 'pending',
                                'uploaded_at' => now(),
                            ]
                        );
                    }
                }
            }

            // 5. Arahan (Redirect) dan Auto-Login
            if ($isNewUser || $user) {
                Auth::login($user); // Login menggunakan instance user yang valid
                
                $msg = $bimtek->butuh_verifikasi_dokumen 
                    ? 'Pendaftaran akun berhasil! Berkas administrasi Anda sedang dalam antrean pemeriksaan panitia.' 
                    : 'Pendaftaran berhasil! Selamat datang di Dasbor.';
                
                return redirect()->route('dashboard')->with('success', $msg);
            }

            return redirect()->route('login')
                ->with('success', 'Pendaftaran kelas berhasil. Silakan login untuk melihat status.');
        });
    }
}