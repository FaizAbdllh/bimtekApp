<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Display laporan index page.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get bimtek list for dropdown
        $bimteks = collect();
        
        if ($user->hasRole(['Admin IT', 'Kepala', 'PPK'])) {
            // Admin, Kepala, PPK bisa lihat semua bimtek
            $bimteks = Bimtek::orderBy('created_at', 'desc')->get();
        } else {
            // User lain hanya bisa lihat bimtek yang mereka terlibat (pivot) atau sebagai PIC langsung
            $bimteks = Bimtek::where(function ($query) use ($user) {
                $query->where('pic_user_id', $user->id)
                    ->orWhereHas('users', function ($subQuery) use ($user) {
                        $subQuery->where('users.id', $user->id);
                    });
            })->orderBy('created_at', 'desc')->get();
        }

        return view('laporan.index', compact('bimteks'));
    }

    /**
     * Generate laporan rekap peserta (PDF).
     */
    public function rekapPeserta(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::with(['users' => function ($query) {
            $query->orderBy('name');
        }, 'pengajuan'])->findOrFail($request->bimtek_id);

        // Authorization
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $panitia = $bimtek->panitia()->orderBy('name')->get();
        $pemateri = collect($bimtek->daftar_pemateri_array ?? []);
        $pic = $bimtek->pic; // Direct relation, not collection

        $data = [
            'bimtek' => $bimtek,
            'peserta' => $peserta,
            'panitia' => $panitia,
            'pemateri' => $pemateri,
            'pic' => $pic,
            'tanggal_cetak' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.rekap-peserta', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Rekap_Peserta_' . str_replace(' ', '_', $bimtek->judul_final) . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate laporan rekap absensi (PDF).
     */
    public function rekapAbsensi(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::with(['sesiAbsensis.absensis.user', 'peserta'])
            ->findOrFail($request->bimtek_id);

        // Authorization
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $sesiAbsensis = $bimtek->sesiAbsensis()->orderBy('created_at')->get();

        // Build attendance matrix
        $rekapAbsensi = [];
        foreach ($peserta as $p) {
            $rekapAbsensi[$p->id] = [
                'user' => $p,
                'kehadiran' => [],
                'total_hadir' => 0,
            ];
            
            foreach ($sesiAbsensis as $sesi) {
                $hadir = $sesi->absensis->contains('user_id', $p->id);
                $rekapAbsensi[$p->id]['kehadiran'][$sesi->id] = $hadir;
                if ($hadir) {
                    $rekapAbsensi[$p->id]['total_hadir']++;
                }
            }
        }

        $data = [
            'bimtek' => $bimtek,
            'sesiAbsensis' => $sesiAbsensis,
            'rekapAbsensi' => $rekapAbsensi,
            'totalSesi' => $sesiAbsensis->count(),
            'tanggal_cetak' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.rekap-absensi', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = 'Rekap_Absensi_' . str_replace(' ', '_', $bimtek->judul_final) . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate laporan rekap nilai tugas (PDF).
     */
    public function rekapNilai(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::with(['tugas.pengumpulanTugas.user', 'peserta'])
            ->findOrFail($request->bimtek_id);

        // Authorization
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $tugasList = $bimtek->tugas()->orderBy('created_at')->get();

        // Build nilai matrix
        $rekapNilai = [];
        foreach ($peserta as $p) {
            $rekapNilai[$p->id] = [
                'user' => $p,
                'nilai' => [],
                'total_nilai' => 0,
                'jumlah_tugas_dikerjakan' => 0,
            ];
            
            foreach ($tugasList as $tugas) {
                $pengumpulan = $tugas->pengumpulanTugas->where('user_id', $p->id)->first();
                $nilai = $pengumpulan?->nilai;
                $rekapNilai[$p->id]['nilai'][$tugas->id] = $nilai;
                
                if ($pengumpulan) {
                    $rekapNilai[$p->id]['jumlah_tugas_dikerjakan']++;
                    if ($nilai !== null) {
                        $rekapNilai[$p->id]['total_nilai'] += $nilai;
                    }
                }
            }
            
            // Calculate average
            $jumlahDinilai = collect($rekapNilai[$p->id]['nilai'])->filter(fn($v) => $v !== null)->count();
            $rekapNilai[$p->id]['rata_rata'] = $jumlahDinilai > 0 
                ? round($rekapNilai[$p->id]['total_nilai'] / $jumlahDinilai, 2) 
                : null;
        }

        $data = [
            'bimtek' => $bimtek,
            'tugasList' => $tugasList,
            'rekapNilai' => $rekapNilai,
            'tanggal_cetak' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.rekap-nilai', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $bimtek->judul_final) . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Generate laporan daftar bimtek (PDF).
     */
    public function daftarBimtek(Request $request)
    {
        $request->validate([
            'tahun' => 'nullable|integer|min:2020|max:2099',
            'status' => 'nullable|in:semua,persiapan,berlangsung,selesai',
        ]);

        $user = Auth::user();
        $tahun = $request->tahun ?? now()->year;
        $status = $request->status ?? 'semua';

        // Authorization - hanya Admin IT, Kepala, PPK
        if (!$user->hasRole(['Admin IT', 'Kepala', 'PPK'])) {
            abort(403, 'Anda tidak memiliki akses untuk laporan ini.');
        }

        $query = Bimtek::with(['pengajuan', 'pic', 'peserta'])
            ->whereYear('created_at', $tahun);

        if ($status !== 'semua') {
            $query->where('status_pelaksanaan', $status);
        }

        $bimteks = $query->orderBy('created_at', 'desc')->get();

        $data = [
            'bimteks' => $bimteks,
            'tahun' => $tahun,
            'status' => $status,
            'tanggal_cetak' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.daftar-bimtek', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = "Daftar_Bimtek_{$tahun}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Generate laporan kegiatan bimtek lengkap (PDF).
     */
    public function laporanKegiatan(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::with([
            'pengajuan.kebutuhanAnggarans',
            'pengajuan.fasilitasLogistiks',
            'users',
            'materis',
            'tugas',
            'sesiAbsensis.absensis',
            'sertifikats',
        ])->findOrFail($request->bimtek_id);

        // Authorization
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $panitia = $bimtek->panitia()->orderBy('name')->get();

        // Hitung statistik
        $totalPeserta = $peserta->count();
        $totalMateri = $bimtek->materis->count();
        $totalTugas = $bimtek->tugas->count();
        $totalSesi = $bimtek->sesiAbsensis->count();
        $totalSertifikat = $bimtek->sertifikats->count();

        // Rata-rata kehadiran
        $avgKehadiran = 0;
        if ($totalSesi > 0 && $totalPeserta > 0) {
            $totalKehadiran = $bimtek->sesiAbsensis->sum(fn($sesi) => $sesi->absensis->count());
            $avgKehadiran = round(($totalKehadiran / ($totalSesi * $totalPeserta)) * 100, 1);
        }

        $data = [
            'bimtek' => $bimtek,
            'peserta' => $peserta,
            'panitia' => $panitia,
            'pic' => $bimtek->pic, // Direct relation
            'totalPeserta' => $totalPeserta,
            'totalMateri' => $totalMateri,
            'totalTugas' => $totalTugas,
            'totalSesi' => $totalSesi,
            'totalSertifikat' => $totalSertifikat,
            'avgKehadiran' => $avgKehadiran,
            'tanggal_cetak' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.laporan-kegiatan', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Kegiatan_' . str_replace(' ', '_', $bimtek->judul_final) . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export rekap peserta to Excel/CSV.
     */
    public function exportPesertaExcel(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::findOrFail($request->bimtek_id);
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();

        $filename = 'Peserta_' . str_replace(' ', '_', $bimtek->judul_final) . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bimtek, $peserta) {
            $file = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Title
            fputcsv($file, ['DAFTAR PESERTA BIMTEK']);
            fputcsv($file, [$bimtek->judul_final]);
            fputcsv($file, ['Tanggal: ' . ($bimtek->tanggal_mulai_final ? $bimtek->tanggal_mulai_final->format('d/m/Y') . ' - ' . $bimtek->tanggal_selesai_final?->format('d/m/Y') : '-')]);
            fputcsv($file, ['']);
            
            // Header
            fputcsv($file, ['No', 'Nama', 'Email', 'NIP', 'Asal Instansi']);

            // Data
            $no = 1;
            foreach ($peserta as $p) {
                fputcsv($file, [
                    $no++,
                    $p->name,
                    $p->email,
                    $p->nip ?? '-',
                    $p->asal_instansi ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export rekap absensi to Excel/CSV.
     */
    public function exportAbsensiExcel(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::with(['sesiAbsensis.absensis'])->findOrFail($request->bimtek_id);
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $sesiAbsensis = $bimtek->sesiAbsensis()->orderBy('created_at')->get();

        $filename = 'Absensi_' . str_replace(' ', '_', $bimtek->judul_final) . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bimtek, $peserta, $sesiAbsensis) {
            $file = fopen('php://output', 'w');
            
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Title
            fputcsv($file, ['REKAP ABSENSI BIMTEK']);
            fputcsv($file, [$bimtek->judul_final]);
            fputcsv($file, ['']);
            
            // Header
            $header = ['No', 'Nama', 'Email'];
            foreach ($sesiAbsensis as $sesi) {
                $header[] = $sesi->nama_sesi;
            }
            $header[] = 'Total Hadir';
            $header[] = 'Persentase';
            fputcsv($file, $header);

            // Data
            $no = 1;
            $totalSesi = $sesiAbsensis->count();
            
            foreach ($peserta as $p) {
                $row = [$no++, $p->name, $p->email];
                $totalHadir = 0;
                
                foreach ($sesiAbsensis as $sesi) {
                    $hadir = $sesi->absensis->contains('user_id', $p->id);
                    $row[] = $hadir ? 'Hadir' : '-';
                    if ($hadir) $totalHadir++;
                }
                
                $row[] = $totalHadir . '/' . $totalSesi;
                $row[] = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100, 1) . '%' : '0%';
                
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check if user has access to bimtek.
     */
    private function authorizeBimtekAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();

        // Admin, Kepala, PPK bisa akses semua
        if ($user->hasRole(['Admin IT', 'Kepala', 'PPK'])) {
            return;
        }

        // User lain harus terlibat di bimtek (pivot) atau sebagai PIC langsung
        $hasAccess = $bimtek->pic_user_id === $user->id
            || $bimtek->users()->where('users.id', $user->id)->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke bimtek ini.');
        }
    }
}
