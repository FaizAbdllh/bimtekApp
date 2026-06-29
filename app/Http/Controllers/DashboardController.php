<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $data = $this->getDashboardData($user);
        return view('dashboard', $data);
    }

    protected function getDashboardData(User $user): array
    {
        $data = [
            'user' => $user,
            'role' => $user->role->nama_peran ?? 'Unknown',
        ];

        if ($user->isAdminIt()) {
            $data = array_merge($data, $this->getAdminItData());
        } elseif ($user->isKepala()) {
            $data = array_merge($data, $this->getKepalaData());
        } elseif ($user->isPpk()) {
            $data = array_merge($data, $this->getPpkData());
        } elseif ($user->isRt()) {
            $data = array_merge($data, $this->getRtData());
        } elseif ($user->isPegawaiInternal()) {
            $data = array_merge($data, $this->getPegawaiInternalData($user));
        } elseif ($user->isPesertaEksternal()) {
            $data = array_merge($data, $this->getPesertaEksternalData($user));
        }

        return $data;
    }

    protected function getAdminItData(): array
    {
        return [
            'totalUsers' => User::count(),
            'totalBimtek' => Bimtek::count(),
            'totalPengajuan' => Bimtek::whereIn('status', ['diajukan', 'disetujui_kepala', 'disetujui_ppk'])->count(),
            'bimtekAktif' => Bimtek::where('status', 'berlangsung')->count(),
            'recentPengajuan' => Bimtek::with(['pic'])
                ->whereIn('status', ['diajukan', 'disetujui_kepala', 'disetujui_ppk'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function getKepalaData(): array
    {
        return [
            'pengajuanMenunggu' => Bimtek::where('status', 'diajukan')->count(),
            'pengajuanDisetujui' => Bimtek::whereIn('status', ['disetujui_kepala', 'disetujui_ppk', 'disetujui_final'])->count(),
            'pengajuanDitolak' => Bimtek::where('status', 'ditolak')->count(),
            'recentPengajuan' => Bimtek::where('status', 'diajukan')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function getPpkData(): array
    {
        return [
            'pengajuanMenungguPpk' => Bimtek::where('status', 'disetujui_kepala')->count(),
            'pengajuanDisetujuiPpk' => Bimtek::whereIn('status', ['disetujui_ppk', 'disetujui_final'])->count(),
            'totalAnggaran' => Bimtek::sum('anggaran_disetujui'),
            'recentPengajuan' => Bimtek::where('status', 'disetujui_kepala')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function getRtData(): array
    {
        $stats = Bimtek::whereIn('status', ['disetujui_final', 'persiapan', 'berlangsung'])
            ->selectRaw("
                SUM(CASE WHEN status_rt = 'belum_dipenuhi' THEN 1 ELSE 0 END) as belum_dipenuhi,
                SUM(CASE WHEN status_rt = 'telah_dipenuhi' THEN 1 ELSE 0 END) as telah_dipenuhi
            ")
            ->first();

        return [
            'kebutuhanBelumDipenuhi' => $stats->belum_dipenuhi ?? 0,
            'kebutuhanTerpenuhi' => $stats->telah_dipenuhi ?? 0,
            'recentPengajuan' => Bimtek::whereIn('status', ['disetujui_final', 'persiapan'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function getPegawaiInternalData(User $user): array
    {
        $pengajuanStats = Bimtek::where('pic_user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'disetujui_final' THEN 1 ELSE 0 END) as disetujui
            ")
            ->first();

        $bimtekPanitiaCount = $user->bimteksSebagaiPanitia()->count();
        $bimtekPicCount = $user->bimteksSebagaiPic()->count();

        return [
            'pengajuanSaya' => $pengajuanStats->total ?? 0,
            'pengajuanDisetujui' => $pengajuanStats->disetujui ?? 0,
            'bimtekSaya' => $bimtekPanitiaCount + $bimtekPicCount,
            'bimtekSebagaiPic' => $bimtekPicCount,
            'recentPengajuan' => Bimtek::where('pic_user_id', $user->id)->latest()->take(5)->get(),
            'bimtekAktif' => $user->bimteksSebagaiPanitia()->where('status', 'berlangsung')->take(5)->get(),
        ];
    }

    protected function getPersuratanData(): array
    {
        return [
            // Persuratan mengurus berkas surat undangan yang belum diunggah pasca finalisasi
            'bimtekMenungguFinal' => Bimtek::where('status', 'disetujui_final')->whereNull('file_surat_undangan_path')->count(),
            'bimtekSuratSelesai' => Bimtek::whereNotNull('file_surat_undangan_path')->count(),
            'recentBimtekMenunggu' => Bimtek::where('status', 'disetujui_final')->whereNull('file_surat_undangan_path')->latest()->take(5)->get(),
            'recentBimtekSelesai' => Bimtek::whereNotNull('file_surat_undangan_path')->latest()->take(5)->get(),
        ];
    }

    protected function getPesertaEksternalData(User $user): array
    {
        return [
            'bimtekDiikuti' => $user->bimteksSebagaiPeserta()->count(),
            'bimtekSelesai' => $user->bimteksSebagaiPeserta()->where('status', 'selesai')->count(),
            'sertifikatDiperoleh' => $user->sertifikats()->count(),
            'bimtekAktif' => $user->bimteksSebagaiPeserta()->whereIn('status', ['persiapan', 'berlangsung'])->take(5)->get(),
            'tugasMenunggu' => 0,
        ];
    }
}