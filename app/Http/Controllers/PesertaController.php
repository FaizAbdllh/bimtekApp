<?php

namespace App\Http\Controllers;

use App\Mail\PesertaAddedToBimtekMail;
use App\Mail\PesertaBimtekInvitedMail;
use App\Mail\PesertaCredentialsMail;
use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PesertaController extends Controller
{
    /**
     * Tampilkan daftar seluruh peserta yang tergabung di dalam kelas Bimtek.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->authorizeAccess($bimtek);

        $bimtek->load(['peserta', 'pic', 'panitia']);

        $canManage = $this->canManage($bimtek);
        $availableUsers = collect();

        if ($canManage) {
            $assignedUserIds = array_merge(
                $bimtek->panitia->pluck('id')->toArray(),
                $bimtek->peserta->pluck('id')->toArray(),
                [$bimtek->pic_user_id]
            );

            // 💡 Ambil ID untuk role Peserta Eksternal
            $pesertaRole = Role::where('nama_peran', 'Peserta Eksternal')->first();

            $availableUsers = User::whereNotIn('id', $assignedUserIds)
                ->when($pesertaRole, function ($query) use ($pesertaRole) {
                    // Pastikan hanya role Peserta Eksternal yang muncul di daftar pilihan
                    return $query->where('role_id', $pesertaRole->id);
                })
                ->orderBy('name')
                ->get();
        }

        $activatedUserIds = $bimtek->peserta()
            ->where('users.is_active', 1) 
            ->pluck('users.id')
            ->toArray();

        $totalPesertaIds = $bimtek->peserta->pluck('id')->toArray();
        $pesertaPendingCount = count(array_diff($totalPesertaIds, $activatedUserIds));

        return view('peserta.index', compact('bimtek', 'canManage', 'availableUsers', 'pesertaPendingCount'));
    }

    /**
     * Daftarkan pegawai internal/eksternal yang sudah memiliki akun ke dalam kelas Bimtek sebagai peserta.
     */
    public function store(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ], [
            'user_ids.required' => 'Pilih minimal 1 peserta.',
            'user_ids.min' => 'Pilih minimal 1 peserta.',
        ]);

        $addedCount = 0;
        $skippedCount = 0;

        foreach ($validated['user_ids'] as $userId) {
            $isRegistered = $bimtek->peserta()->where('user_id', $userId)->exists() 
                || $bimtek->panitia()->where('user_id', $userId)->exists()
                || $bimtek->pic_user_id === $userId;

            if ($isRegistered) {
                $skippedCount++;
                continue;
            }

            $pivotData = [
                'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'verified',
            ];

            $bimtek->peserta()->attach($userId, $pivotData);

            $user = User::find($userId);

            if ($bimtek->butuh_verifikasi_dokumen) {
                try {
                    // 💡 Kirim email pemberitahuan + link upload dokumen (karena akun sudah aktif)
                    Mail::to($user->email)->send(new PesertaBimtekInvitedMail(
                        $bimtek,
                        $user,
                        route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id)
                    ));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email undangan verifikasi dokumen: '.$e->getMessage());
                }
            } else {
                try {
                    Mail::to($user->email)->send(new PesertaAddedToBimtekMail(
                        $bimtek,
                        $user,
                        route('login')
                    ));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email notifikasi peserta: '.$e->getMessage());
                }
            }

            $addedCount++;
        }

        $message = "Berhasil menambahkan {$addedCount} peserta.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} user dilewati (sudah terdaftar).";
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek->id)
            ->with('success', $message);
    }

    /**
     * Buat akun baru sekaligus daftarkan aktor tersebut ke kelas Bimtek sebagai peserta eksternal.
     */
    public function storeNew(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nip' => 'nullable|string|max:50|unique:users,nip',
            'asal_instansi' => 'nullable|string|max:255',
        ]);

        $password = Str::random(10);
        $pesertaRole = Role::where('nama_peran', 'Peserta Eksternal')->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'nip' => $validated['nip'] ?? null,
            'asal_instansi' => $validated['asal_instansi'] ?? null,
            'role_id' => $pesertaRole?->id,
            'is_active' => 1, // 💡 Pastikan akun baru langsung aktif!
        ]);

        $pivotData = [
            'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'verified',
        ];

        $bimtek->peserta()->attach($user->id, $pivotData);

        $emailSent = false;
        try {
            // Mengirim email berisi password yang di-generate sistem
            Mail::to($user->email)->send(new PesertaCredentialsMail($user, $password, $bimtek));
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email kredensial: '.$e->getMessage());
        }

        // 💡 PERBAIKAN: Hapus blok Token Aktivasi! Langsung arahkan ke halaman upload jika butuh dokumen.
        if ($bimtek->butuh_verifikasi_dokumen) {
            try {
                $uploadUrl = route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id);
                Mail::to($user->email)->send(new PesertaBimtekInvitedMail($bimtek, $user, $uploadUrl));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email undangan verifikasi dokumen: '.$e->getMessage());
            }
        }

        $message = "Peserta {$user->name} berhasil ditambahkan.";
        $message .= $emailSent ? ' Email kredensial telah dikirim.' : " Password: {$password} (email gagal dikirim)";

        return redirect()
            ->route('bimtek.peserta.index', $bimtek->id)
            ->with('success', $message)
            ->with('new_user_password', $emailSent ? null : $password);
    }

    /**
     * Memproses unggahan berkas massal CSV nama-nama peserta eksternal.
     */
    public function import(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');

        $addedCount = 0;
        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $errors = [];
        $newUsers = [];

        $pesertaRole = Role::where('nama_peran', 'Peserta Eksternal')->first();
        $handle = fopen($file->getRealPath(), 'r');

        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            rewind($handle);
        }

        $header = null;
        $firstLine = fgets($handle);
        rewind($handle);
        
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            rewind($handle);
        }
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($header === null) {
                $header = array_map(function ($h) {
                    $h = trim($h);
                    return strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h));
                }, $row);
                continue;
            }

            $data = [];
            foreach ($header as $index => $columnName) {
                $data[$columnName] = isset($row[$index]) ? trim($row[$index]) : '';
            }

            $name = $data['nama'] ?? $data['name'] ?? $data['nama lengkap'] ?? $data['nama_lengkap'] ?? '';
            $email = $data['email'] ?? $data['e-mail'] ?? '';
            $nip = $data['nip'] ?? '';
            $instansi = $data['instansi'] ?? $data['asal_instansi'] ?? $data['asal instansi'] ?? '';

            if (empty($name) && empty($email)) {
                continue; 
            }

            if (empty($name) || empty($email)) {
                $errorCount++;
                $barisKe = $addedCount + $skippedCount + $errorCount;
                $errors[] = "Baris {$barisKe}: Nama dan Email wajib diisi.";
                continue;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorCount++;
                $barisKe = $addedCount + $skippedCount + $errorCount;
                $errors[] = "Baris {$barisKe}: Format email '{$email}' tidak valid.";
                continue;
            }

            $user = User::where('email', $email)->first();
            $isNewUser = false;
            $password = null;

            if (! $user) {
                if (! empty($nip) && User::where('nip', $nip)->exists()) {
                    $errorCount++;
                    $barisKe = $addedCount + $skippedCount + $errorCount;
                    $errors[] = "Baris {$barisKe}: NIP '{$nip}' sudah terdaftar.";
                    continue;
                }

                $password = Str::random(10);
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'nip' => ! empty($nip) ? $nip : null,
                    'asal_instansi' => ! empty($instansi) ? $instansi : null,
                    'role_id' => $pesertaRole?->id,
                    'is_active' => 1, // 💡 Akun import CSV langsung aktif!
                ]);

                $isNewUser = true;
                $createdCount++;
            }

            if ($bimtek->peserta()->where('user_id', $user->id)->exists()) {
                $skippedCount++;
                continue;
            }

            $pivotData = [
                'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'verified',
            ];

            $bimtek->peserta()->attach($user->id, $pivotData);
            $addedCount++;

            // 💡 PERBAIKAN: Proses pengiriman email pada saat CSV import (Tanpa logika token aktivasi)
            if ($isNewUser && $password) {
                try {
                    Mail::to($user->email)->send(new PesertaCredentialsMail($user, $password, $bimtek));
                    $newUsers[] = ['name' => $name, 'email' => $email, 'password' => '(dikirim via email)'];
                } catch (\Exception $e) {
                    $newUsers[] = ['name' => $name, 'email' => $email, 'password' => $password.' (email gagal)'];
                }

                if ($bimtek->butuh_verifikasi_dokumen) {
                    try {
                        // Langsung arahkan login lalu isi dokumen
                        $uploadUrl = route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id);
                        Mail::to($user->email)->send(new PesertaBimtekInvitedMail($bimtek, $user, $uploadUrl));
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim email undangan dokumen hasil import.");
                    }
                }
            } else {
                try {
                    if ($bimtek->butuh_verifikasi_dokumen) {
                        Mail::to($user->email)->send(new PesertaBimtekInvitedMail($bimtek, $user, route('bimtek.verifikasi-dokumen.upload-form', $bimtek->id)));
                    } else {
                        Mail::to($user->email)->send(new PesertaAddedToBimtekMail($bimtek, $user, route('login')));
                    }
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim notifikasi email peserta eksisting.");
                }
            }
        } 

        fclose($handle);

        $message = "Import selesai. {$addedCount} peserta ditambahkan ke bimtek.";
        if ($createdCount > 0) {
            $message .= " {$createdCount} akun baru dibuat.";
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek->id)
            ->with('success', $message)
            ->with('import_errors', $errors)
            ->with('new_users', $newUsers);
    }

    /**
     * Keluarkan seorang peserta dari keanggotaan kelas Bimtek.
     */
    public function destroy(Bimtek $bimtek, User $user): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $isPeserta = $bimtek->peserta()->where('user_id', $user->id)->exists();

        if (! $isPeserta) {
            return redirect()->route('bimtek.peserta.index', $bimtek->id)->with('error', 'User bukan peserta bimtek ini.');
        }

        $bimtek->peserta()->detach($user->id);

        return redirect()
            ->route('bimtek.peserta.index', $bimtek->id)
            ->with('success', "Peserta {$user->name} berhasil dihapus dari bimtek.");
    }

    /**
     * Fitur Checklist Massal: Mengeluarkan banyak peserta sekaligus dari kelas.
     */
    public function bulkDestroy(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $removedCount = 0;

        foreach ($validated['user_ids'] as $userId) {
            $isPeserta = $bimtek->peserta()->where('user_id', $userId)->exists();

            if ($isPeserta) {
                $bimtek->peserta()->detach($userId);
                $removedCount++;
            }
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek->id)
            ->with('success', "Berhasil menghapus {$removedCount} peserta.");
    }

    /**
     * Mengubah peran kontekstual aktor di dalam kegiatan kelas.
     */
    public function changeRole(Request $request, Bimtek $bimtek, User $user): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'peran' => 'required|in:peserta,panitia',
        ]);

        if ($validated['peran'] === 'panitia' && ! $user->isPegawaiInternal()) {
            return redirect()->route('bimtek.peserta.index', $bimtek->id)->with('error', 'Hanya user dengan role Pegawai Internal yang dapat diubah menjadi panitia.');
        }

        $isPeserta = $bimtek->peserta()->where('user_id', $user->id)->exists();
        $isPanitia = $bimtek->panitia()->where('user_id', $user->id)->exists();

        if (! $isPeserta && ! $isPanitia) {
            return redirect()->route('bimtek.peserta.index', $bimtek->id)->with('error', 'User tidak terdaftar di bimtek ini.');
        }

        if ($validated['peran'] === 'panitia') {
            $bimtek->peserta()->detach($user->id);
            if (! $isPanitia) {
                $bimtek->panitia()->attach($user->id, ['fungsi_panitia' => 'Anggota Tim Pelaksana']);
            }
        } else {
            $bimtek->panitia()->detach($user->id);
            if (! $isPeserta) {
                $bimtek->peserta()->attach($user->id, [
                    'status_verifikasi' => $bimtek->butuh_verifikasi_dokumen ? 'pending' : 'verified'
                ]);
            }
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek->id)
            ->with('success', "Peran {$user->name} berhasil diubah.");
    }

    /**
     * Unduh lembar daftar nama peserta yang ada di kelas ke dalam bentuk CSV Excel.
     */
    public function export(Bimtek $bimtek)
    {
        $this->authorizeAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $filename = 'peserta_'.str_replace(' ', '_', $bimtek->judul_final).'_'.date('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($peserta) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Nama', 'Email', 'NIP', 'Instansi'], ';');

            foreach ($peserta as $p) {
                fputcsv($file, [$p->name, $p->email, $p->nip ?? '-', $p->asal_instansi ?? '-'], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_peserta.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Nama', 'Email', 'NIP', 'Instansi'], ';');
            fputcsv($file, ['Ahmad Hidayat', 'ahmad.hidayat@gmail.com', '198501012010011001', 'Dinas Pendidikan Kota Padang'], ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Gerbang Validasi Kebijakan Otoritas Pengguna
     */
    private function canManage(Bimtek $bimtek): bool
    {
        $user = Auth::user();
        return $bimtek->pic_user_id === $user->id || $bimtek->panitia()->where('user_id', $user->id)->exists();
    }

    private function authorizeAccess(Bimtek $bimtek): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk()) {
            return;
        }

        if ($bimtek->pic_user_id === $user->id) {
            return;
        }

        $hasAccess = $bimtek->peserta()->where('user_id', $user->id)->exists() 
            || $bimtek->panitia()->where('user_id', $user->id)->exists();

        if (! $hasAccess) {
            abort(403, 'Anda tidak memiliki hak akses informasi peserta pada kelas ini.');
        }
    }

    private function authorizeManage(Bimtek $bimtek): void
    {
        if (! $this->canManage($bimtek)) {
            abort(403, 'Akses ditolak. Anda bukan pengelola kelas ini.');
        }
    }
}