<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\DokumenPersyaratanPeserta;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicRegistrationController extends Controller
{
    public function show(\Illuminate\Http\Request $request, Bimtek $bimtek)
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
        $jenisDokumenWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'nip' => 'nullable|string|max:50',
            'asal_instansi' => 'nullable|string|max:255',
        ];

        if ($bimtek->butuh_verifikasi_dokumen) {
            foreach ($jenisDokumenWajib as $jenis) {
                $rules["dokumen.{$jenis}"] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
            }
        }

        $validated = $request->validate($rules, [
            'dokumen.*.required' => 'Dokumen wajib diunggah.',
            'dokumen.*.mimes' => 'File harus PDF/JPG/PNG.',
            'dokumen.*.max' => 'Ukuran file maksimal 2MB.',
        ]);

        // If bimtek has invite_code set, require it in input (either hidden or form)
        if ($bimtek->invite_code) {
            $code = $request->input('invite_code') ?? $request->query('code');
            if ($code !== $bimtek->invite_code) {
                return back()->withErrors(['invite_code' => 'Kode undangan tidak cocok atau tidak diberikan.']);
            }
        }

        // Find or create user
        $user = null;
        if (!empty($validated['email'])) {
            $user = User::where('email', $validated['email'])->first();
        }
        if (!$user && !empty($validated['nip'])) {
            $user = User::where('nip', $validated['nip'])->first();
        }

        if (!$user) {
            // Create user with placeholder email if none provided
            $email = $validated['email'] ?? ('no-email+' . Str::uuid() . '@example.local');
            $password = Str::random(12);
            $pesertaRole = Role::where('nama_peran', 'Peserta Eksternal')->first();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $email,
                'password' => Hash::make($password),
                'nip' => $validated['nip'] ?? null,
                'asal_instansi' => $validated['asal_instansi'] ?? null,
                'role_id' => $pesertaRole?->id,
            ]);
        }

        // Attach to bimtek if not already
        if (!$bimtek->users()->where('users.id', $user->id)->exists()) {
            $pivotData = [
                'id' => (string) Str::uuid(),
                'peran_kontekstual' => 'peserta',
            ];
            if ($bimtek->butuh_verifikasi_dokumen) {
                $pivotData['status_verifikasi'] = 'invited';
                $pivotData['notified_at'] = now();
            }
            $bimtek->users()->attach($user->id, $pivotData);
        }

        // Store uploaded documents if any
        if ($request->hasFile('dokumen')) {
            foreach ($request->file('dokumen') as $jenis => $file) {
                if (!$file) continue;
                $fileName = time() . '_' . Str::slug($jenis) . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('dokumen_persyaratan', $fileName, 'public');

                DokumenPersyaratanPeserta::create([
                    'bimtek_id' => $bimtek->id,
                    'user_id' => $user->id,
                    'jenis_dokumen' => $jenis,
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'status' => 'pending',
                    'uploaded_at' => now(),
                ]);
            }

            // Update pivot status to pending
            \Illuminate\Support\Facades\DB::table('bimtek_user')
                ->where('bimtek_id', $bimtek->id)
                ->where('user_id', $user->id)
                ->update(['status_verifikasi' => 'pending']);
        }

        // Log the user in
        Auth::login($user);

        if ($bimtek->butuh_verifikasi_dokumen) {
            return redirect()->route('bimtek.verifikasi-dokumen.upload-form', $bimtek)
                ->with('success', 'Registrasi berhasil. Silakan unggah dokumen persyaratan.');
        }

        return redirect()->route('bimtek.show.peserta', $bimtek)->with('success', 'Registrasi berhasil.');
    }
}
