<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard sesuai role user
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Data dashboard berbeda sesuai role
        $data = $this->getDashboardData($user);
        
        return view('dashboard', $data);
    }

    /**
     * Get dashboard data berdasarkan role user
     */
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

    /**
     * Dashboard data untuk Admin IT
     */
    protected function getAdminItData(): array
    {
        return [
            'totalUsers' => User::count(),
            'totalBimtek' => Bimtek::count(),
            'totalPengajuan' => Pengajuan::count(),
            'bimtekAktif' => Bimtek::where('status_pelaksanaan', 'berlangsung')->count(),
            'recentPengajuan' => Pengajuan::with(['user'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Dashboard data untuk Kepala
     */
    protected function getKepalaData(): array
    {
        return [
            'pengajuanMenunggu' => Pengajuan::where('status_pengajuan', 'diajukan')->count(),
            // Disetujui = sudah disetujui Kepala (termasuk yang sudah lanjut ke PPK dan final)
            'pengajuanDisetujui' => Pengajuan::whereIn('status_pengajuan', ['disetujui_kepala', 'disetujui_ppk', 'disetujui_final'])->count(),
            'pengajuanDitolak' => Pengajuan::where('status_pengajuan', 'ditolak')->count(),
            'recentPengajuan' => Pengajuan::with(['user'])
                ->where('status_pengajuan', 'diajukan')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Dashboard data untuk PPK
     */
    protected function getPpkData(): array
    {
        return [
            'pengajuanMenungguPpk' => Pengajuan::where('status_pengajuan', 'disetujui_kepala')->count(),
            // Disetujui PPK = yang sudah disetujui anggaran (termasuk final)
            'pengajuanDisetujuiPpk' => Pengajuan::whereIn('status_pengajuan', ['disetujui_ppk', 'disetujui_final'])->count(),
            'totalAnggaran' => Bimtek::sum('anggaran_disetujui'),
            'recentPengajuan' => Pengajuan::with(['user'])
                ->where('status_pengajuan', 'disetujui_kepala')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Dashboard data untuk Koordinator RT
     */
    protected function getRtData(): array
    {
        // Optimize with single query for statistics
        $stats = Pengajuan::where('status_pengajuan', 'disetujui_final')
            ->whereHas('fasilitasLogistiks')
            ->selectRaw("
                SUM(CASE WHEN status_rt = 'belum_dipenuhi' THEN 1 ELSE 0 END) as belum_dipenuhi,
                SUM(CASE WHEN status_rt = 'telah_dipenuhi' THEN 1 ELSE 0 END) as telah_dipenuhi
            ")
            ->first();

        return [
            'kebutuhanBelumDipenuhi' => $stats->belum_dipenuhi ?? 0,
            'kebutuhanTerpenuhi' => $stats->telah_dipenuhi ?? 0,
            'recentPengajuan' => Pengajuan::with(['user', 'fasilitasLogistiks'])
                ->where('status_pengajuan', 'disetujui_final')
                ->whereHas('fasilitasLogistiks')
                ->where('status_rt', '!=', 'telah_dipenuhi')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Dashboard data untuk Pegawai Internal
     */
    protected function getPegawaiInternalData(User $user): array
    {
        // Optimize pengajuan queries
        $pengajuanStats = Pengajuan::where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status_pengajuan = 'disetujui_final' THEN 1 ELSE 0 END) as disetujui
            ")
            ->first();

        // Load bimtek data once
        $bimtekData = $user->bimteks()->withPivot('peran_kontekstual')->get();
        
        // Count bimtek sebagai PIC (dari kolom pic_user_id)
        $bimtekSebagaiPic = Bimtek::where('pic_user_id', $user->id)->count();

        return [
            'pengajuanSaya' => $pengajuanStats->total ?? 0,
            'pengajuanDisetujui' => $pengajuanStats->disetujui ?? 0,
            'bimtekSaya' => $bimtekData->count(),
            'bimtekSebagaiPic' => $bimtekSebagaiPic,
            'recentPengajuan' => Pengajuan::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
            'bimtekAktif' => $bimtekData
                ->where('status_pelaksanaan', 'berlangsung')
                ->take(5),
        ];
    }

    /**
     * Dashboard data untuk Peserta Eksternal
     */
    protected function getPesertaEksternalData(User $user): array
    {
        return [
            'bimtekDiikuti' => $user->bimteks()->count(),
            'bimtekSelesai' => $user->bimteks()->where('status_pelaksanaan', 'selesai')->count(),
            'sertifikatDiperoleh' => $user->sertifikats()->count(),
            'bimtekAktif' => $user->bimteks()
                ->whereIn('status_pelaksanaan', ['persiapan', 'berlangsung'])
                ->take(5)
                ->get(),
            'tugasMenunggu' => 0, // Will be calculated later
        ];
    }
}
