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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesertaController extends Controller
{
    /**
     * Display a listing of peserta for a bimtek.
     */
    public function index(Bimtek $bimtek): View
    {
        $this->authorizeAccess($bimtek);

        $bimtek->load(['peserta', 'pic', 'panitia']);

        $canManage = $this->canManage($bimtek);

        // Get available users for adding (excluding already assigned users)
        $assignedUserIds = $bimtek->users()->pluck('users.id')->toArray();
        $availableUsers = collect();

        if ($canManage) {
            $availableUsers = User::whereNotIn('id', $assignedUserIds)
                ->orderBy('name')
                ->get();
        }

        // =====================================================================
        // PERBAIKAN: Hitung peserta "Menunggu Aktivasi" secara efisien
        // =====================================================================
        // 1. Ambil semua ID user yang SUDAH sukses aktivasi di bimtek ini
        $activatedUserIds = \App\Models\ActivationToken::where('bimtek_id', $bimtek->id)
            ->whereNotNull('used_at')
            ->pluck('user_id')
            ->toArray();

        // 2. Ambil semua ID peserta yang terdaftar di bimtek ini
        $totalPesertaIds = $bimtek->peserta->pluck('id')->toArray();

        // 3. Hitung selisihnya (Total Peserta dikurangi yang Sudah Aktivasi)
        $pesertaPendingCount = count(array_diff($totalPesertaIds, $activatedUserIds));

        // Tambahkan 'pesertaPendingCount' ke dalam compact() agar terlempar ke Blade
        return view('peserta.index', compact('bimtek', 'canManage', 'availableUsers', 'pesertaPendingCount'));
    }

    /**
     * Store a newly created peserta assignment (existing users).
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
            // Check if user is already assigned to this bimtek
            $exists = $bimtek->users()->where('users.id', $userId)->exists();

            if ($exists) {
                $skippedCount++;

                continue;
            }

            // Attach peserta
            $pivotData = [
                'id' => (string) Str::uuid(),
                'peran_kontekstual' => 'peserta',
            ];

            // If bimtek requires document verification, set initial status
            if ($bimtek->butuh_verifikasi_dokumen) {
                $pivotData['status_verifikasi'] = 'invited';
                $pivotData['notified_at'] = now();
            }

            $bimtek->users()->attach($userId, $pivotData);

            // Send appropriate email notification
            $user = User::find($userId);

            if ($bimtek->butuh_verifikasi_dokumen) {
                // Send email for document verification (Existing User: Direct to Upload Form)
                try {
                    Mail::to($user->email)->send(new PesertaBimtekInvitedMail(
                        $bimtek,
                        $user,
                        route('bimtek.verifikasi-dokumen.upload-form', $bimtek)
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send invitation email for document verification: '.$e->getMessage());
                    \App\Models\LogSistem::warning(
                        "Gagal mengirim email undangan verifikasi dokumen ke {$user->email} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                        Auth::id()
                    );
                }
            } else {
                // Send general notification email (no document verification required)
                try {
                    Mail::to($user->email)->send(new PesertaAddedToBimtekMail(
                        $bimtek,
                        $user,
                        route('login')
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send peserta added notification email: '.$e->getMessage());
                    \App\Models\LogSistem::warning(
                        "Gagal mengirim email notifikasi peserta ke {$user->email} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                        Auth::id()
                    );
                }
            }

            $addedCount++;
        }

        $message = "Berhasil menambahkan {$addedCount} peserta.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} user dilewati (sudah terdaftar).";
        }

        if ($addedCount > 0) {
            if ($bimtek->butuh_verifikasi_dokumen) {
                $message .= ' Email undangan verifikasi dokumen telah dikirim.';
            } else {
                $message .= ' Email notifikasi telah dikirim ke peserta.';
            }
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek)
            ->with('success', $message);
    }

    /**
     * Store a new peserta with new user account.
     */
    public function storeNew(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nip' => 'nullable|string|max:50|unique:users,nip',
            'asal_instansi' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
        ]);

        // Generate random password
        $password = Str::random(10);

        // Get default role for external peserta (Peserta Eksternal or similar)
        $pesertaRole = Role::where('nama_peran', 'Peserta Eksternal')->first();

        // Create new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'nip' => $validated['nip'] ?? null,
            'asal_instansi' => $validated['asal_instansi'] ?? null,
            'role_id' => $pesertaRole?->id,
        ]);

        // Attach to bimtek as peserta
        $pivotData = [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'peserta',
        ];

        // If bimtek requires document verification, set initial status
        if ($bimtek->butuh_verifikasi_dokumen) {
            $pivotData['status_verifikasi'] = 'invited';
            $pivotData['notified_at'] = now();
        }

        $bimtek->users()->attach($user->id, $pivotData);

        // Send email with login credentials
        $emailSent = false;
        try {
            Mail::to($user->email)->send(new PesertaCredentialsMail($user, $password, $bimtek));
            $emailSent = true;
        } catch (\Exception $e) {
            Log::error('Failed to send credentials email: '.$e->getMessage());
            \App\Models\LogSistem::warning(
                "Gagal mengirim email kredensial peserta baru ke {$user->email} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                Auth::id()
            );
        }

        // =====================================================================
        // FIX: Jika user baru & butuh verifikasi, arahkan tombol email ke Aktivasi Form
        // =====================================================================
        if ($bimtek->butuh_verifikasi_dokumen) {
            try {
                // Parameter ke-2 diisi angka 7 (hari), parameter ke-3 diisi ID panitia yang sedang login
                [$tokenModel, $rawToken] = \App\Models\ActivationToken::generateFor($user, 7, Auth::id());
                
                // Rakit URL menuju halaman manual activation form
                $uploadUrl = route('activation.form') . '?email=' . urlencode($user->email) . '&token=' . $rawToken;

                Mail::to($user->email)->send(new PesertaBimtekInvitedMail(
                    $bimtek,
                    $user,
                    $uploadUrl
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send invitation email for document verification: '.$e->getMessage());
                \App\Models\LogSistem::warning(
                    "Gagal mengirim email undangan verifikasi dokumen ke {$user->email} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                    Auth::id()
                );
            }
        }

        $message = "Peserta {$user->name} berhasil ditambahkan.";
        if ($emailSent) {
            $message .= ' Email kredensial telah dikirim.';
        } else {
            $message .= " Password: {$password} (email gagal dikirim)";
        }

        if ($bimtek->butuh_verifikasi_dokumen) {
            $message .= ' Email undangan verifikasi dokumen telah dikirim.';
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek)
            ->with('success', $message)
            ->with('new_user_password', $emailSent ? null : $password);
    }

    /**
     * Remove the specified peserta from bimtek.
     */
    public function destroy(Bimtek $bimtek, User $user): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        // Check if user is assigned as peserta
        $isPeserta = $bimtek->peserta()->where('users.id', $user->id)->exists();

        if (! $isPeserta) {
            return redirect()
                ->route('bimtek.peserta.index', $bimtek)
                ->with('error', 'User bukan peserta bimtek ini.');
        }

        // Detach the user
        $bimtek->users()->detach($user->id);

        return redirect()
            ->route('bimtek.peserta.index', $bimtek)
            ->with('success', "Peserta {$user->name} berhasil dihapus dari bimtek.");
    }

    /**
     * Bulk remove peserta from bimtek.
     */
    public function bulkDestroy(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ], [
            'user_ids.required' => 'Pilih minimal 1 peserta.',
        ]);

        $removedCount = 0;

        foreach ($validated['user_ids'] as $userId) {
            $isPeserta = $bimtek->peserta()->where('users.id', $userId)->exists();

            if ($isPeserta) {
                $bimtek->users()->detach($userId);
                $removedCount++;
            }
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek)
            ->with('success', "Berhasil menghapus {$removedCount} peserta.");
    }

    /**
     * Change user role in bimtek (peserta <-> panitia).
     */
    public function changeRole(Request $request, Bimtek $bimtek, User $user): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $validated = $request->validate([
            'peran' => 'required|in:peserta,panitia',
        ]);

        if ($validated['peran'] === 'panitia' && ! $user->isPegawaiInternal()) {
            return redirect()
                ->route('bimtek.peserta.index', $bimtek)
                ->with('error', 'Hanya user dengan role Pegawai Internal yang dapat diubah menjadi panitia.');
        }

        // Check if user is assigned to this bimtek
        $assignment = $bimtek->users()->where('users.id', $user->id)->first();

        if (! $assignment) {
            return redirect()
                ->route('bimtek.peserta.index', $bimtek)
                ->with('error', 'User tidak terdaftar di bimtek ini.');
        }

        // Update role (PIC tidak ada di bimtek_user lagi, hanya panitia/peserta)
        $bimtek->users()->updateExistingPivot($user->id, [
            'peran_kontekstual' => $validated['peran'],
        ]);

        $peranLabel = $validated['peran'] === 'panitia' ? 'Panitia' : 'Peserta';

        return redirect()
            ->route('bimtek.peserta.index', $bimtek)
            ->with('success', "Peran {$user->name} berhasil diubah menjadi {$peranLabel}.");
    }

    /**
     * Import peserta from CSV file (creates new accounts if not exists).
     */
    public function import(Request $request, Bimtek $bimtek): RedirectResponse
    {
        $this->authorizeManage($bimtek);

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ], [
            'file.required' => 'File wajib diupload.',
            'file.mimes' => 'Format file harus CSV.',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $file = $request->file('file');

        $addedCount = 0;
        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $errors = [];
        $newUsers = [];

        // Get default role for external peserta
        $pesertaRole = Role::where('nama_peran', 'Peserta Eksternal')->first();

        $handle = fopen($file->getRealPath(), 'r');

        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            rewind($handle);
        }

        $header = null;
        $lineNumber = 0;

        // Auto-detect delimiter (comma atau semicolon) dari line pertama setelah BOM
        $firstLine = fgets($handle);
        rewind($handle);
        // Skip BOM lagi setelah rewind
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            rewind($handle);
        }
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineNumber++;

            // First row is header
            if ($header === null) {
                // Clean dan lowercase header, remove BOM jika ada
                $header = array_map(function ($h) {
                    $h = trim($h);
                    // Remove BOM dari first column jika ada
                    $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);

                    return strtolower($h);
                }, $row);

                continue;
            }

            // Map row to associative array
            $data = [];
            foreach ($header as $index => $columnName) {
                $data[$columnName] = isset($row[$index]) ? trim($row[$index]) : '';
            }

            // Required: name and email
            // Support various header formats and fallback to column index
            $name = $data['nama'] ?? $data['name'] ?? $data['nama lengkap'] ?? $data['nama_lengkap'] ?? ($data[0] ?? '');
            $email = $data['email'] ?? $data['e-mail'] ?? ($data[1] ?? '');
            $nip = $data['nip'] ?? ($data[2] ?? '');
            $instansi = $data['instansi'] ?? $data['asal_instansi'] ?? $data['asal instansi'] ?? $data['asal-instansi'] ?? ($data[3] ?? '');

            // Skip baris yang benar-benar kosong (tidak ada nama dan email)
            if (empty($name) && empty($email)) {
                continue; // Skip tanpa error
            }

            // Validate required fields (jika ada salah satu, keduanya harus ada)
            if (empty($name) || empty($email)) {
                $errorCount++;
                $errors[] = "Baris {$lineNumber}: Nama dan Email wajib diisi.";

                continue;
            }

            // Validate email format
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorCount++;
                $errors[] = "Baris {$lineNumber}: Format email '{$email}' tidak valid.";

                continue;
            }

            // Check if user exists by email
            $user = User::where('email', $email)->first();
            $isNewUser = false;
            $password = null;

            if (! $user) {
                // Check if NIP exists (if provided)
                if (! empty($nip) && User::where('nip', $nip)->exists()) {
                    $errorCount++;
                    $errors[] = "Baris {$lineNumber}: NIP '{$nip}' sudah terdaftar untuk user lain.";

                    continue;
                }

                // Create new user
                $password = Str::random(10);

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'nip' => ! empty($nip) ? $nip : null,
                    'asal_instansi' => ! empty($instansi) ? $instansi : null,
                    'role_id' => $pesertaRole?->id,
                ]);

                $isNewUser = true;
                $createdCount++;
            }

            // Check if already assigned to this bimtek
            if ($bimtek->users()->where('users.id', $user->id)->exists()) {
                $skippedCount++;

                continue;
            }

            // Attach to bimtek as peserta
            $pivotData = [
                'id' => (string) Str::uuid(),
                'peran_kontekstual' => 'peserta',
            ];

            // If bimtek requires document verification, set initial status
            if ($bimtek->butuh_verifikasi_dokumen) {
                $pivotData['status_verifikasi'] = 'invited';
                $pivotData['notified_at'] = now();
            }

            $bimtek->users()->attach($user->id, $pivotData);
            $addedCount++;

            // Send email notification
            if ($isNewUser && $password) {
                // New user: Send credentials email
                try {
                    Mail::to($user->email)->send(new PesertaCredentialsMail($user, $password, $bimtek));
                    $newUsers[] = [
                        'name' => $name,
                        'email' => $email,
                        'password' => '(dikirim via email)',
                    ];
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$email}: ".$e->getMessage());
                    \App\Models\LogSistem::warning(
                        "Gagal mengirim email kredensial hasil import ke {$email} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                        Auth::id()
                    );
                    $newUsers[] = [
                        'name' => $name,
                        'email' => $email,
                        'password' => $password.' (email gagal)',
                    ];
                }

            // =====================================================================
            // FIX IMPORT: Jika user baru hasil CSV & butuh verifikasi berkas, kirim token aktivasi
            // =====================================================================
            if ($bimtek->butuh_verifikasi_dokumen) {
                try {
                    // Sesuaikan parameter agar menerima int (7 hari) dan ID panitia
                    [$tokenModel, $rawToken] = \App\Models\ActivationToken::generateFor($user, 7, Auth::id());
                    $uploadUrl = route('activation.form') . '?email=' . urlencode($user->email) . '&token=' . $rawToken;

                    Mail::to($user->email)->send(new PesertaBimtekInvitedMail(
                        $bimtek,
                        $user,
                        $uploadUrl
                    ));
                } catch (\Exception $e) {
                    Log::error("Failed to send invitation email to {$email}: ".$e->getMessage());
                }
            }
            } else {
                // Existing user: Send notification based on verification requirement (Direct upload URL)
                try {
                    if ($bimtek->butuh_verifikasi_dokumen) {
                        Mail::to($user->email)->send(new PesertaBimtekInvitedMail(
                            $bimtek,
                            $user,
                            route('bimtek.verifikasi-dokumen.upload-form', $bimtek)
                        ));
                    } else {
                        Mail::to($user->email)->send(new PesertaAddedToBimtekMail(
                            $bimtek,
                            $user,
                            route('login')
                        ));
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send notification email to {$email}: ".$e->getMessage());
                    \App\Models\LogSistem::warning(
                        "Gagal mengirim email notifikasi hasil import ke {$email} pada bimtek {$bimtek->id}: {$e->getMessage()}",
                        Auth::id()
                    );
                }
            }
        }

        fclose($handle);

        // Build result message
        $message = "Import selesai. {$addedCount} peserta ditambahkan ke bimtek.";
        if ($createdCount > 0) {
            $message .= " {$createdCount} akun baru dibuat.";
        }
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} sudah terdaftar di bimtek.";
        }
        if ($errorCount > 0) {
            $message .= " {$errorCount} baris error.";
        }

        return redirect()
            ->route('bimtek.peserta.index', $bimtek)
            ->with('success', $message)
            ->with('import_errors', $errors)
            ->with('new_users', $newUsers);
    }

    /**
     * Export peserta list to CSV.
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

            // Tambahkan BOM untuk Excel agar auto-detect UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header tanpa spasi, gunakan semicolon delimiter untuk Excel
            fputcsv($file, ['Nama', 'Email', 'NIP', 'Instansi'], ';');

            // Data
            foreach ($peserta as $p) {
                fputcsv($file, [
                    $p->name,
                    $p->email,
                    $p->nip ?? '-',
                    $p->asal_instansi ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download template for import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_peserta.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Tambahkan BOM untuk Excel agar auto-detect UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Gunakan semicolon sebagai delimiter (lebih compatible dengan Excel)
            // Header tanpa spasi agar Excel bisa parse dengan benar
            fputcsv($file, ['Nama', 'Email', 'NIP', 'Instansi'], ';');

            // Contoh data hanya 1 baris
            fputcsv($file, ['Ahmad Hidayat', 'ahmad.hidayat@gmail.com', '198501012010011001', 'Dinas Pendidikan Kota Padang'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check if user can manage this bimtek (PIC or Panitia).
     */
    private function canManage(Bimtek $bimtek): bool
    {
        $user = Auth::user();

        // PIC or Panitia can manage peserta
        return $bimtek->pic_user_id === $user->id ||
               $bimtek->panitia()->where('users.id', $user->id)->exists();
    }

    /**
     * Check if user has access to this bimtek.
     */
    private function authorizeAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();

        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk()) {
            return;
        }

        if ($bimtek->pic_user_id === $user->id) {
            return;
        }

        $hasAccess = $bimtek->users()->where('users.id', $user->id)->exists();

        if (! $hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke bimtek ini.');
        }
    }

    /**
     * Check if user can manage this bimtek (PIC or Panitia).
     */
    private function authorizeManage(Bimtek $bimtek): void
    {
        if (! $this->canManage($bimtek)) {
            abort(403, 'Hanya PIC atau Panitia yang dapat mengelola peserta.');
        }
    }
}