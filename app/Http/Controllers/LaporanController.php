<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\User;
use App\Models\AbsensiPeserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman utama dasbor penarikan laporan.
     */
    public function index(): View
    {
        $user = Auth::user();
        $bimteks = collect();

        // REFAKTORISASI: Menyelaraskan hak pengecekan peran menggunakan metode eksplisit
        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk()) {
            // Manajemen puncak & Admin IT berhak menarik laporan dari seluruh kegiatan DIPA
            $bimteks = Bimtek::orderBy('created_at', 'desc')->get();
        } else {
            // Pegawai internal hanya berhak menarik laporan kelas di mana dia menjadi PIC atau Panitia Pokja
            $bimteks = Bimtek::where(function ($query) use ($user) {
                $query->where('pic_user_id', $user->id)
                    ->orWhereHas('panitia', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    })
                    ->orWhereHas('peserta', function ($subQuery) use ($user) {
                        $subQuery->where('user_id', $user->id);
                    });
            })->orderBy('created_at', 'desc')->get();
        }

        return view('laporan.index', compact('bimteks'));
    }

    /**
     * Produksi Dokumen PDF Rekapitulasi Biodata Seluruh Aktor Kelas.
     */
    public function rekapPeserta(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        // REFAKTORISASI: Mencabut eager-loading tabel pengajuans & users lama
        $bimtek = Bimtek::with(['pic'])->findOrFail($request->bimtek_id);

        $this->authorizeBimtekAccess($bimtek);

        // Membagi pengelompokkan data aktor secara terpisah dan rapi
        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $panitia = $bimtek->panitia()->orderBy('name')->get();
        $pemateri = collect($bimtek->daftar_pemateri_array ?? []);
        $pic = $bimtek->pic; 

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

        $filename = 'Rekap_Peserta_'.str_replace(' ', '_', $bimtek->judul_final ?? $bimtek->judul_rencana).'.pdf';
        return $pdf->download($filename);
    }

    /**
     * Produksi Dokumen PDF Matriks Rekapitulasi Presensi Kehadiran Peserta.
     */
    public function rekapAbsensi(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        // REFAKTORISASI: Mengubah rute pemanggilan relasi absensi dari absensis menjadi absensiPesertas
        $bimtek = Bimtek::with(['sesiAbsensis.absensiPesertas', 'peserta'])
            ->findOrFail($request->bimtek_id);

        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $sesiAbsensis = $bimtek->sesiAbsensis()->orderBy('created_at')->get();

        // Membangun kalkulasi matriks kehadiran baris demi baris
        $rekapAbsensi = [];
        foreach ($peserta as $p) {
            $rekapAbsensi[$p->id] = [
                'user' => $p,
                'kehadiran' => [],
                'total_hadir' => 0,
            ];

            foreach ($sesiAbsensis as $sesi) {
                // REFAKTORISASI: Mencocokkan data koleksi objek model absensiPesertas baru
                $hadir = $sesi->absensiPesertas->contains('user_id', $p->id);
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

        // Format lanskap sangat cocok untuk dokumen berbentuk tabel kolom memanjang ke samping
        $pdf = Pdf::loadView('laporan.pdf.rekap-absensi', $data);
        $pdf->setPaper('a4', 'landscape');

        $filename = 'Rekap_Absensi_'.str_replace(' ', '_', $bimtek->judul_final ?? $bimtek->judul_rencana).'.pdf';
        return $pdf->download($filename);
    }

    /**
     * Produksi Dokumen PDF Rekapitulasi Nilai Tugas & Indeks Kelulusan Belajar.
     */
    public function rekapNilai(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::with(['tugas.pengumpulanTugas', 'peserta'])
            ->findOrFail($request->bimtek_id);

        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $tugasList = $bimtek->tugas()->orderBy('created_at')->get();

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

            $jumlahDinilai = collect($rekapNilai[$p->id]['nilai'])->filter(fn ($v) => $v !== null)->count();
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

        $filename = 'Rekap_Nilai_'.str_replace(' ', '_', $bimtek->judul_final ?? $bimtek->judul_rencana).'.pdf';
        return $pdf->download($filename);
    }

    /**
     * Produksi Dokumen PDF Rekapitulasi Laporan Tahunan Seluruh Kegiatan Bimtek BBPMP.
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

        if (! ($user->isAdminIt() || $user->isKepala() || $user->isPpk())) {
            abort(403, 'Anda tidak memiliki hak akses otoritas untuk mengunduh laporan tahunan ini.');
        }

        // REFAKTORISASI: Mengalihkan target kolom pencarian status dari status_pelaksanaan menjadi status
        $query = Bimtek::with(['pic', 'peserta'])->whereYear('created_at', $tahun);

        if ($status !== 'semua') {
            $query->where('status', $status);
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
     * Produksi Dokumen PDF Lembar Laporan Akuntabilitas Kinerja Lengkap Berbasis Kegiatan.
     */
    public function laporanKegiatan(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        // REFAKTORISASI: Penyesuaian nama relasi logistik terpadu & jembatan presensi terpisah
        $bimtek = Bimtek::with([
            'fasilitasLogistiks',
            'materis',
            'tugas',
            'sesiAbsensis.absensiPesertas',
            'sertifikats',
        ])->findOrFail($request->bimtek_id);

        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $panitia = $bimtek->panitia()->orderBy('name')->get();

        $totalPeserta = $peserta->count();
        $totalMateri = $bimtek->materis->count();
        $totalTugas = $bimtek->tugas->count();
        $totalSesi = $bimtek->sesiAbsensis->count();
        $totalSertifikat = $bimtek->sertifikats->count();

        $avgKehadiran = 0;
        if ($totalSesi > 0 && $totalPeserta > 0) {
            $totalKehadiran = $bimtek->sesiAbsensis->sum(fn ($sesi) => $sesi->absensiPesertas->count());
            $avgKehadiran = round(($totalKehadiran / ($totalSesi * $totalPeserta)) * 100, 1);
        }

        $data = [
            'bimtek' => $bimtek,
            'peserta' => $peserta,
            'panitia' => $panitia,
            'pic' => $bimtek->pic,
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

        $filename = 'Laporan_Kegiatan_'.str_replace(' ', '_', $bimtek->judul_final ?? $bimtek->judul_rencana).'.pdf';
        return $pdf->download($filename);
    }

    /**
     * Ekspor Lembar Kerja CSV Excel Daftar Nama Peserta.
     */
    public function exportPesertaExcel(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::findOrFail($request->bimtek_id);
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $filename = 'Peserta_'.str_replace(' ', '_', $bimtek->judul_final ?? $bimtek->judul_rencana).'_'.date('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bimtek, $peserta) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel

            fputcsv($file, ['DAFTAR PESERTA BIMTEK']);
            fputcsv($file, [$bimtek->judul_final ?? $bimtek->judul_rencana]);
            fputcsv($file, ['Tanggal: '.($bimtek->tanggal_mulai_final ? $bimtek->tanggal_mulai_final->format('d/m/Y') : '-')]);
            fputcsv($file, ['']);

            fputcsv($file, ['No', 'Nama', 'Email', 'NIP', 'Asal Instansi']);

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
     * Ekspor Lembar Kerja CSV Excel Matriks Kehadiran Presensi Lengkap.
     */
    public function exportAbsensiExcel(Request $request)
    {
        $request->validate([
            'bimtek_id' => 'required|exists:bimteks,id',
        ]);

        $bimtek = Bimtek::with(['sesiAbsensis.absensiPesertas'])->findOrFail($request->bimtek_id);
        $this->authorizeBimtekAccess($bimtek);

        $peserta = $bimtek->peserta()->orderBy('name')->get();
        $sesiAbsensis = $bimtek->sesiAbsensis()->orderBy('created_at')->get();

        $filename = 'Absensi_'.str_replace(' ', '_', $bimtek->judul_final ?? $bimtek->judul_rencana).'_'.date('Ymd').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bimtek, $peserta, $sesiAbsensis) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REKAP ABSENSI BIMTEK']);
            fputcsv($file, [$bimtek->judul_final ?? $bimtek->judul_rencana]);
            fputcsv($file, ['']);

            $header = ['No', 'Nama', 'Email'];
            foreach ($sesiAbsensis as $sesi) {
                $header[] = $sesi->nama_sesi;
            }
            $header[] = 'Total Hadir';
            $header[] = 'Persentase';
            fputcsv($file, $header);

            $no = 1;
            $totalSesi = $sesiAbsensis->count();

            foreach ($peserta as $p) {
                $row = [$no++, $p->name, $p->email];
                $totalHadir = 0;

                foreach ($sesiAbsensis as $sesi) {
                    $hadir = $sesi->absensiPesertas->contains('user_id', $p->id);
                    $row[] = $hadir ? 'Hadir' : '-';
                    if ($hadir) {
                        $totalHadir++;
                    }
                }

                $row[] = $totalHadir.'/'.$totalSesi;
                $row[] = $totalSesi > 0 ? round(($totalHadir / $totalSesi) * 100, 1).'%' : '0%';

                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Gerbang Perlindungan Otoritas Penarikan Laporan Internal.
     */
    private function authorizeBimtekAccess(Bimtek $bimtek): void
    {
        $user = Auth::user();

        if ($user->isAdminIt() || $user->isKepala() || $user->isPpk()) {
            return;
        }

        $hasAccess = $bimtek->pic_user_id === $user->id
            || $bimtek->panitia()->where('user_id', $user->id)->exists()
            || $bimtek->peserta()->where('user_id', $user->id)->exists();

        if (! $hasAccess) {
            abort(403, 'Akses ditolak. Anda tidak berhak menarik laporan dari kelas ini.');
        }
    }
}