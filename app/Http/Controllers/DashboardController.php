<?php

namespace App\Http\Controllers;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // 1. Proteksi Auth: Mencegah crash jika session expired
        if (!$user) {
            return redirect()->route('login');
        }

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
        $stats = Bimtek::whereIn('status', ['persiapan', 'registrasi', 'persiapan_selesai', 'berlangsung'])
            ->selectRaw("
                SUM(CASE WHEN status_rt = 'belum_dipenuhi' THEN 1 ELSE 0 END) as belum_dipenuhi,
                SUM(CASE WHEN status_rt = 'telah_dipenuhi' THEN 1 ELSE 0 END) as telah_dipenuhi
            ")
            ->first();

        return [
            'kebutuhanBelumDipenuhi' => $stats->belum_dipenuhi ?? 0,
            'kebutuhanTerpenuhi' => $stats->telah_dipenuhi ?? 0,
            // 2. Data RT Terurut: Ditambahkan latest() agar menampilkan prioritas logistik terbaru
            'recentPengajuan' => Bimtek::whereIn('status', ['persiapan', 'registrasi', 'persiapan_selesai'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    protected function getPegawaiInternalData(User $user): array
    {
        // 3. Perbaikan Logika Pengajuan Disetujui:
        // Menangkap seluruh fase setelah pengajuan disetujui (disetujui_final hingga selesai)
        $pengajuanStats = Bimtek::where('pic_user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('disetujui_final', 'persiapan', 'registrasi', 'persiapan_selesai', 'berlangsung', 'selesai') THEN 1 ELSE 0 END) as disetujui
            ")
            ->first();

        // 4. Cegah Penghitungan Ganda PIC & Panitia:
        // Menggunakan distinct relasi (jika PIC ATAU menjadi bagian dari Panitia, hitung 1 kali saja)
        $bimtekSayaCount = Bimtek::where('pic_user_id', $user->id)
            ->orWhereHas('panitia', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->count();

        $bimtekPicCount = $user->bimteksSebagaiPic()->count();

        return [
            'pengajuanSaya' => $pengajuanStats->total ?? 0,
            'pengajuanDisetujui' => (int) ($pengajuanStats->disetujui ?? 0),
            'bimtekSaya' => $bimtekSayaCount,
            'bimtekSebagaiPic' => $bimtekPicCount,
            'recentPengajuan' => Bimtek::where('pic_user_id', $user->id)->latest()->take(5)->get(),
            'bimtekAktif' => $user->bimteksSebagaiPanitia()->where('status', 'berlangsung')->latest()->take(5)->get(),
        ];
    }

    protected function getPesertaEksternalData(User $user): array
    {
        return [
            'bimtekDiikuti' => $user->bimteksSebagaiPeserta()->count(),
            'bimtekSelesai' => $user->bimteksSebagaiPeserta()->where('status', 'selesai')->count(),
            'sertifikatDiperoleh' => $user->sertifikats()->count(),
            // 5. Perbaikan Status Peserta Eksternal:
            // Peserta eksternal hanya terlibat saat fase registrasi, persiapan selesai, dan berlangsung
            'bimtekAktif' => $user->bimteksSebagaiPeserta()
                ->whereIn('status', ['registrasi', 'persiapan_selesai', 'berlangsung'])
                ->latest()
                ->take(5)
                ->get(),
            'tugasMenunggu' => 0,
        ];
    }
}