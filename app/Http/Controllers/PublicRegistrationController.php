<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\DokumenPersyaratanPeserta;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PublicRegistrationController extends Controller
{
    public function show(Request $request, Bimtek $bimtek)
    {
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        // If bimtek has an invite_code set, require it in query param
        $requiredCode = $bimtek->invite_code;
        $provided = $request->query('code') ?? $request->query('invite_code');
        if ($requiredCode && $provided !== $requiredCode) {
            return view('public.invite-required', compact('bimtek'));
        }

        return view('public.daftar', compact('bimtek', 'jenisDokumenWajib'));
    }

    public function register(Request $request, Bimtek $bimtek)
    {
        // PERBAIKAN: Mengubah email menjadi required sesuai dengan standar keamanan gerbang baru
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255', 
            'nip' => 'nullable|string|max:50',
            'asal_instansi' => 'nullable|string|max:255',
        ];

        // PERBAIKAN: Validasi berkas unggahan secara dinamis jika Bimtek memerlukan dokumen kedinasan
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];
        if ($bimtek->butuh_verifikasi_dokumen) {
            foreach ($jenisDokumenWajib as $jenis) {
                $rules["dokumen.$jenis"] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
            }
        }

        $validated = $request->validate($rules);

        if ($bimtek->invite_code) {
            $code = $request->input('invite_code') ?? $request->query('code');
            if ($code !== $bimtek->invite_code) {
                return back()->withErrors(['invite_code' => 'Kode undangan tidak cocok atau tidak diberikan.']);
            }
        }

        // Cari atau buat user baru
        $user = null;
        if (! empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
        }
        if (! $user && ! empty($validated['nip'])) {
            $user = User::where('nip', $validated['nip'])->first();
        }

        if (! $user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(12)),
                'nip' => $validated['nip'] ?? null,
                'asal_instansi' => $validated['asal_instansi'] ?? null,
                'role_id' => Role::where('nama_peran', 'Peserta Eksternal')->value('id'),
            ]);
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? $user->email,
            'nip' => $validated['nip'] ?? $user->nip,
            'asal_instansi' => $validated['asal_instansi'] ?? $user->asal_instansi,
        ]);

        // Hubungkan peserta ke kegiatan Bimtek melalui tabel pivot
        $bimtek->users()->syncWithoutDetaching([
            $user->id => [
                'id' => (string) Str::uuid(),
                'peran_kontekstual' => 'peserta',
                'status_verifikasi' => 'pending',
                'notified_at' => now(),
            ],
        ]);

        // =====================================================================
        // PERBAIKAN UTAMA: Pemrosesan & Penyimpanan Berkas Surat Tugas & SPPD
        // =====================================================================
        if ($bimtek->butuh_verifikasi_dokumen && $request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $jenis => $file) {
                // Simpan fisik file ke storage
                $path = $file->store('dokumen_peserta', 'public');
                $originalName = $file->getClientOriginalName();

                // Menggunakan firstOrNew untuk mengamankan baris data & mencegah duplikasi
                $dokumenLog = DokumenPersyaratanPeserta::firstOrNew([
                    'user_id'       => $user->id,
                    'bimtek_id'     => $bimtek->id,
                    'jenis_dokumen' => $jenis, // Menyimpan teks 'surat_tugas' atau 'sppd'
                ]);

                // Jika data benar-benar baru, buatkan UUID manual untuk Primary Key string Anda
                if (!$dokumenLog->exists) {
                    $dokumenLog->id = (string) Str::uuid();
                }

                // Isi sisa kolom sesuai dengan skema teks Anda
                $dokumenLog->file_path   = $path;
                $dokumenLog->file_name   = $originalName;
                $dokumenLog->status      = 'Pending';
                $dokumenLog->uploaded_at = now();
                $dokumenLog->save();
            }
        }

        return view('public.registration-success', [
            'bimtek' => $bimtek,
            'email' => $user->email,
            'message' => 'Registrasi berhasil. Silakan tunggu token dari panitia.',
        ]);
    }
}