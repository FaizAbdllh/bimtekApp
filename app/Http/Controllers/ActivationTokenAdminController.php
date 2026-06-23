<?php

namespace App\Http\Controllers;

use App\Models\ActivationToken;
use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Http\Request;
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

            [$tokenModel, $raw] = ActivationToken::generateFor($user, $days, $request->user()?->id);
            $tokenModel->bimtek_id = $bimtek->id;
            $tokenModel->save();

            $rows[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $raw,
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

    public function revoke(Request $request, Bimtek $bimtek, ActivationToken $activationToken)
    {
        $activationToken->revoked_at = now();
        $activationToken->revoked_by = $request->user()?->id;
        $activationToken->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('status', 'Token revoked');
    }
}