<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Admin\LogSistemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BimtekController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RtController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\TugasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard - accessible by all authenticated users
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================
// ROUTES BERDASARKAN ROLE
// =====================

// Admin IT routes
Route::middleware(['auth', 'role:Admin IT'])->prefix('admin')->name('admin.')->group(function () {
    // User management
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    Route::get('log-sistem', [LogSistemController::class, 'index'])->name('log-sistem.index');
    Route::get('log-sistem/export', [LogSistemController::class, 'export'])->name('log-sistem.export');
    Route::post('log-sistem/clear-all', [LogSistemController::class, 'clearAll'])->name('log-sistem.clear-all');
    Route::get('log-sistem/{logSistem}', [LogSistemController::class, 'show'])->name('log-sistem.show');
    Route::delete('log-sistem/{logSistem}', [LogSistemController::class, 'destroy'])->name('log-sistem.destroy');
});

// Kepala routes - Approval pengajuan
Route::middleware(['auth', 'role:Kepala'])->prefix('kepala')->name('approval.kepala.')->group(function () {
    Route::get('pengajuan', [ApprovalController::class, 'indexKepala'])->name('index');
    Route::get('pengajuan/{pengajuan}', [ApprovalController::class, 'showKepala'])->name('show');
    Route::post('pengajuan/{pengajuan}/approve', [ApprovalController::class, 'approveKepala'])->name('approve');
    Route::post('pengajuan/{pengajuan}/reject', [ApprovalController::class, 'rejectKepala'])->name('reject');
    Route::post('pengajuan/{pengajuan}/revisi', [ApprovalController::class, 'revisiKepala'])->name('revisi');
});

// PPK routes - Approval anggaran
Route::middleware(['auth', 'role:PPK'])->prefix('ppk')->name('approval.ppk.')->group(function () {
    Route::get('pengajuan', [ApprovalController::class, 'indexPpk'])->name('index');
    Route::get('pengajuan/{pengajuan}', [ApprovalController::class, 'showPpk'])->name('show');
    Route::post('pengajuan/{pengajuan}/approve', [ApprovalController::class, 'approvePpk'])->name('approve');
    Route::post('pengajuan/{pengajuan}/reject', [ApprovalController::class, 'rejectPpk'])->name('reject');
    Route::post('pengajuan/{pengajuan}/revisi', [ApprovalController::class, 'revisiPpk'])->name('revisi');
});

// Koordinator RT routes - Pemenuhan Fasilitas & Logistik
Route::middleware(['auth', 'role:Koordinator RT'])->prefix('rt')->name('rt.')->group(function () {
    Route::get('/', [RtController::class, 'index'])->name('index');
    Route::get('/{pengajuan}', [RtController::class, 'show'])->name('show');
    Route::patch('/{pengajuan}', [RtController::class, 'update'])->name('update');
});

// Pengajuan Bimtek - Pegawai Internal bisa CRUD, Admin IT/Kepala/PPK bisa lihat semua
Route::middleware(['auth', 'role:Admin IT,Kepala,PPK,Pegawai Internal'])->group(function () {
    Route::resource('pengajuan', PengajuanController::class);
});

// Routes untuk semua internal BBPMP (bisa diakses oleh Admin IT, Kepala, PPK, RT, Pegawai Internal)
Route::middleware(['auth', 'role:Admin IT,Kepala,PPK,Koordinator RT,Pegawai Internal'])
    ->prefix('internal')
    ->name('internal.')
    ->group(function () {
        // Shared routes untuk internal staff
    });

// Routes untuk peserta bimtek (diakses berdasarkan peran dalam bimtek)
Route::middleware(['auth'])->prefix('bimtek')->name('bimtek.')->group(function () {
    // List bimtek
    Route::get('/', [BimtekController::class, 'index'])->name('index');
    
    // Detail bimtek
    Route::get('/{bimtek}', [BimtekController::class, 'show'])->name('show');
    
    // Assign Panitia
    Route::post('/{bimtek}/assign-panitia', [BimtekController::class, 'assignPanitia'])->name('assign-panitia');

    // Kelola Pemateri
    Route::patch('/{bimtek}/pemateri', [BimtekController::class, 'updatePemateri'])->name('pemateri.update');

    // Ajukan revisi pengajuan oleh PIC
    Route::post('/{bimtek}/ajukan-revisi', [BimtekController::class, 'requestRevisi'])->name('request-revisi');
    
    // Update Status
    Route::patch('/{bimtek}/status', [BimtekController::class, 'updateStatus'])->name('update-status');
    
    // Upload Surat Undangan Draft
    Route::post('/{bimtek}/upload-draft', [BimtekController::class, 'uploadDraft'])->name('upload-draft');
    // Backward-compatible alias used by legacy tests/flow
    Route::post('/{bimtek}/upload-undangan', [BimtekController::class, 'uploadDraft'])->name('upload-undangan');
    
    // Preview & Download Surat Draft
    Route::get('/{bimtek}/preview-draft', [BimtekController::class, 'previewDraft'])->name('preview-draft');
    Route::get('/{bimtek}/download-draft', [BimtekController::class, 'downloadDraft'])->name('download-draft');

    // Upload Surat Undangan Final (by Persuratan)
    Route::post('/{bimtek}/upload-final', [BimtekController::class, 'uploadFinal'])->name('upload-final');
    
    // Preview & Download Surat Final
    Route::get('/{bimtek}/preview-final', [BimtekController::class, 'previewFinal'])->name('preview-final');
    Route::get('/{bimtek}/download-final', [BimtekController::class, 'downloadFinal'])->name('download-final');
    
    // Verifikasi Dokumen routes
    Route::get('/{bimtek}/verifikasi-dokumen', [\App\Http\Controllers\VerifikasiDokumenController::class, 'index'])->name('verifikasi-dokumen.index');
    Route::get('/{bimtek}/upload-dokumen', [\App\Http\Controllers\VerifikasiDokumenController::class, 'uploadForm'])->name('verifikasi-dokumen.upload-form');
    Route::post('/{bimtek}/upload-dokumen', [\App\Http\Controllers\VerifikasiDokumenController::class, 'upload'])->name('verifikasi-dokumen.upload');
    Route::post('/dokumen/{dokumen}/approve', [\App\Http\Controllers\VerifikasiDokumenController::class, 'approve'])->name('verifikasi-dokumen.approve');
    Route::post('/dokumen/{dokumen}/reject', [\App\Http\Controllers\VerifikasiDokumenController::class, 'reject'])->name('verifikasi-dokumen.reject');
    Route::get('/dokumen/{dokumen}/preview', [\App\Http\Controllers\VerifikasiDokumenController::class, 'preview'])->name('verifikasi-dokumen.preview');
    Route::get('/dokumen/{dokumen}/download', [\App\Http\Controllers\VerifikasiDokumenController::class, 'download'])->name('verifikasi-dokumen.download');
    
    // Materi routes
    Route::get('/{bimtek}/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/{bimtek}/materi/create', [MateriController::class, 'create'])->name('materi.create');
    Route::post('/{bimtek}/materi', [MateriController::class, 'store'])->name('materi.store');
    Route::get('/{bimtek}/materi/{materi}/edit', [MateriController::class, 'edit'])->name('materi.edit');
    Route::put('/{bimtek}/materi/{materi}', [MateriController::class, 'update'])->name('materi.update');
    Route::delete('/{bimtek}/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');
    Route::get('/{bimtek}/materi/{materi}/preview', [MateriController::class, 'preview'])->name('materi.preview')->middleware('verified.peserta');
    Route::get('/{bimtek}/materi/{materi}/download', [MateriController::class, 'download'])->name('materi.download')->middleware('verified.peserta');

    // Tugas routes (protected by verification middleware)
    Route::get('/{bimtek}/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/{bimtek}/tugas/create', [TugasController::class, 'create'])->name('tugas.create');
    Route::post('/{bimtek}/tugas', [TugasController::class, 'store'])->name('tugas.store');
    Route::get('/{bimtek}/tugas/{tugas}', [TugasController::class, 'show'])->name('tugas.show')->middleware('verified.peserta');
    Route::get('/{bimtek}/tugas/{tugas}/edit', [TugasController::class, 'edit'])->name('tugas.edit');
    Route::put('/{bimtek}/tugas/{tugas}', [TugasController::class, 'update'])->name('tugas.update');
    Route::delete('/{bimtek}/tugas/{tugas}', [TugasController::class, 'destroy'])->name('tugas.destroy');
    
    // Tugas file routes
    Route::get('/{bimtek}/tugas/{tugas}/preview-instruksi', [TugasController::class, 'previewInstruksi'])->name('tugas.preview-instruksi')->middleware('verified.peserta');
    Route::get('/{bimtek}/tugas/{tugas}/download-instruksi', [TugasController::class, 'downloadInstruksi'])->name('tugas.download-instruksi')->middleware('verified.peserta');
    
    // Pengumpulan tugas routes (protected by verification middleware)
    Route::post('/{bimtek}/tugas/{tugas}/submit', [TugasController::class, 'submit'])->name('tugas.submit')->middleware('verified.peserta');
    Route::get('/{bimtek}/tugas/{tugas}/pengumpulan/{pengumpulan}/preview', [TugasController::class, 'previewJawaban'])->name('tugas.preview-jawaban');
    Route::get('/{bimtek}/tugas/{tugas}/pengumpulan/{pengumpulan}/download', [TugasController::class, 'downloadJawaban'])->name('tugas.download-jawaban');
    Route::post('/{bimtek}/tugas/{tugas}/pengumpulan/{pengumpulan}/grade', [TugasController::class, 'grade'])->name('tugas.grade');

    // Absensi routes (protected by verification middleware)
    Route::get('/{bimtek}/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/{bimtek}/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::post('/{bimtek}/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/{bimtek}/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::get('/{bimtek}/absensi/{sesi}', [AbsensiController::class, 'show'])->name('absensi.show')->middleware('verified.peserta');
    Route::get('/{bimtek}/absensi/{sesi}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit');
    Route::put('/{bimtek}/absensi/{sesi}', [AbsensiController::class, 'update'])->name('absensi.update');
    Route::delete('/{bimtek}/absensi/{sesi}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
    Route::patch('/{bimtek}/absensi/{sesi}/toggle-status', [AbsensiController::class, 'toggleStatus'])->name('absensi.toggle-status');
    Route::post('/{bimtek}/absensi/{sesi}/tambah-kehadiran', [AbsensiController::class, 'tambahKehadiran'])->name('absensi.tambah-kehadiran');
    Route::delete('/{bimtek}/absensi/{sesi}/kehadiran/{absensi}', [AbsensiController::class, 'hapusKehadiran'])->name('absensi.hapus-kehadiran');
    // QR Code routes
    Route::get('/{bimtek}/absensi/{sesi}/qr', [AbsensiController::class, 'showQr'])->name('absensi.show-qr');
    Route::get('/{bimtek}/absensi/{sesi}/scan', [AbsensiController::class, 'scanInterface'])->name('absensi.scan-interface')->middleware('verified.peserta');
    Route::post('/{bimtek}/absensi/{sesi}/scan', [AbsensiController::class, 'scanQr'])->name('absensi.scan-qr')->middleware('verified.peserta');
    Route::post('/{bimtek}/absensi/{sesi}/hadir-online', [AbsensiController::class, 'hadirOnline'])->name('absensi.hadir-online')->middleware('verified.peserta');

    // Sertifikat routes (protected by verification middleware)
    Route::get('/{bimtek}/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
    Route::post('/{bimtek}/sertifikat/generate', [SertifikatController::class, 'generate'])->name('sertifikat.generate');
    Route::get('/{bimtek}/sertifikat/{sertifikat}/preview', [SertifikatController::class, 'preview'])->name('sertifikat.preview')->middleware('verified.peserta');
    Route::get('/{bimtek}/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download'])->name('sertifikat.download')->middleware('verified.peserta');
    Route::delete('/{bimtek}/sertifikat/{sertifikat}', [SertifikatController::class, 'destroy'])->name('sertifikat.destroy');

    // Peserta routes
    Route::get('/{bimtek}/peserta', [PesertaController::class, 'index'])->name('peserta.index');
    Route::post('/{bimtek}/peserta', [PesertaController::class, 'store'])->name('peserta.store');
    Route::post('/{bimtek}/peserta/new', [PesertaController::class, 'storeNew'])->name('peserta.store-new');
    Route::delete('/{bimtek}/peserta/{user}', [PesertaController::class, 'destroy'])->name('peserta.destroy');
    Route::delete('/{bimtek}/peserta', [PesertaController::class, 'bulkDestroy'])->name('peserta.bulk-destroy');
    Route::patch('/{bimtek}/peserta/{user}/change-role', [PesertaController::class, 'changeRole'])->name('peserta.change-role');
    Route::post('/{bimtek}/peserta/import', [PesertaController::class, 'import'])->name('peserta.import');
    Route::get('/{bimtek}/peserta/export', [PesertaController::class, 'export'])->name('peserta.export');
});

// Download template (tidak perlu bimtek)
Route::middleware(['auth'])->get('/peserta/download-template', [PesertaController::class, 'downloadTemplate'])->name('bimtek.peserta.download-template');

// Laporan routes
// Admin IT/Kepala/PPK dapat akses lintas bimtek, Pegawai Internal dibatasi ke bimtek yang terlibat
Route::middleware(['auth', 'role:Admin IT,Kepala,PPK,Pegawai Internal'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::post('/rekap-peserta', [LaporanController::class, 'rekapPeserta'])->name('rekap-peserta');
    Route::post('/rekap-absensi', [LaporanController::class, 'rekapAbsensi'])->name('rekap-absensi');
    Route::post('/rekap-nilai', [LaporanController::class, 'rekapNilai'])->name('rekap-nilai');
    Route::post('/daftar-bimtek', [LaporanController::class, 'daftarBimtek'])->name('daftar-bimtek');
    Route::post('/laporan-kegiatan', [LaporanController::class, 'laporanKegiatan'])->name('laporan-kegiatan');
    Route::get('/export-peserta-excel', [LaporanController::class, 'exportPesertaExcel'])->name('export-peserta-excel');
    Route::get('/export-absensi-excel', [LaporanController::class, 'exportAbsensiExcel'])->name('export-absensi-excel');
});

require __DIR__.'/auth.php';
