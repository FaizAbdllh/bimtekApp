<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivationTokenAdminController extends Controller
{
    public function generateBatch(Request $request, Bimtek $bimtek)
    {
        $request->validate([
            'peserta_ids' => 'required|array',
            'peserta_ids.*' => 'required|uuid|exists:users,id',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        // =====================================================================
        // PROTEKSI SERVER: Validasi Kelayakan Berkas Masuk Mandiri (Surat Tugas & SPPD)
        // =====================================================================
        if ($bimtek->butuh_verifikasi_dokumen) {
            $jenisWajib = $bimtek->jenis_dokumen_wajib ?? ['surat_tugas', 'sppd'];
            $jumlahWajib = is_array($jenisWajib) ? count($jenisWajib) : 2;

            foreach ($request->input('peserta_ids') as $userId) {
                $approvedCount = \App\Models\DokumenPersyaratanPeserta::where('user_id', $userId)
                    ->where('bimtek_id', $bimtek->id)
                    ->where('status', 'Approved')
                    ->count();

                if ($approvedCount < $jumlahWajib) {
                    $userTerlanggar = User::find($userId);
                    $namaPeserta = $userTerlanggar ? $userTerlanggar->name : 'Peserta';
                    
                    return redirect()->back()->with('error', "Gagal memproses batch token! Berkas milik \"{$namaPeserta}\" belum lengkap atau belum disetujui.");
                }
            }
        }

        $days = $request->input('days', 7);
        $rows = [];

        foreach ($request->input('peserta_ids') as $userId) {
            $user = User::find($userId);
            if (! $user) {
                continue;
            }

            // 💡 PERBAIKAN UTAMA: Generate token acak dan simpan langsung ke tabel 'users'
            $rawToken = Str::random(64);
            
            $user->update([
                'activation_token' => $rawToken,
                'token_expires_at' => now()->addDays($days),
                'is_active' => 0, // Pastikan status kembali mengunci sebelum aktivasi sukses
            ]);

            $rows[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $rawToken,
            ];
        }

        $filename = 'activation_tokens_'.$bimtek->id.'_'.date('Ymd_His').'.csv';

        $response = new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['user_id', 'name', 'email', 'token']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['user_id'], $r['name'], $r['email'], $r['token']]);
            }
            fclose($out);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    // 💡 PERBAIKAN UTAMA: Parameter diubah dari 'ActivationToken' menjadi model 'User' langsung
    public function revoke(Request $request, Bimtek $bimtek, User $user)
    {
        // Mengosongkan token langsung di dalam tabel 'users' milik peserta terkait
        $user->update([
            'activation_token' => null,
            'token_expires_at' => null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'Token revoked');
    }
}