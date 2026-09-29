<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\SesiAbsensi;
use App\Models\BimtekPemateri;
use App\Models\Materi;
use App\Models\FasilitasLogistik;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class BimtekStateMachineValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;
    protected User $panitia;
    protected Bimtek $bimtek;
    protected Role $roleInternal;

    protected function setUp(): void
    {
        parent::setUp();

        // Mencegah file terunggah ke storage asli selama pengujian
        Storage::fake('public');

        $this->roleInternal = Role::create(['nama_peran' => 'Pegawai Internal']);
        
        // Memastikan akun PIC berstatus aktif sesuai Aturan 2
        $this->pic = User::factory()->create([
            'role_id' => $this->roleInternal->id, 
            'is_active' => true 
        ]);
        
        $this->panitia = User::factory()->create([
            'role_id' => $this->roleInternal->id
        ]);

        $this->bimtek = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'status' => 'persiapan',
            'judul_rencana' => 'Bimtek Test',
            'deskripsi_rencana' => 'Deskripsi kegiatan pengujian', // Tambahan wajib
            'tempat_kegiatan_rencana' => 'Ruang Rapat Utama', // Tambahan wajib
            'jenis_kegiatan' => 'internal', // Tambahan wajib
            'sumber_pembiayaan' => 'DIPA', // Tambahan wajib
            'jumlah_peserta' => 50, // Tambahan wajib (> 0)
            'mode_pelaksanaan' => 'offline', // Tambahan wajib
            'file_surat_undangan_path' => 'uploads/surat/undangan_dummy.pdf', // Tambahan wajib
            'butuh_verifikasi_dokumen' => false, // Set false agar tidak meminta relasi syarat_dokumens di setup awal
            'tanggal_mulai_rencana' => now()->addDays(1),
            'tanggal_selesai_rencana' => now()->addDays(2),
        ]);

        // ✅ FIX: Gunakan panitia() bukan users()
        $this->bimtek->panitia()->attach($this->panitia->id, [
            'fungsi_panitia' => 'Koordinator',
        ]);
    }

    // ==================== TESTS FOR canOpenRegistration() ====================
    
    #[Test]
    public function can_open_registration_when_all_requirements_met(): void
    {
        // 1. Tentukan path file palsunya
        $dummyPath = 'surat-undangan/surat-dummy.pdf';

        // 2. Suntikkan file fisik kosong ke dalam Fake Storage agar fungsi Storage::exists() bernilai true
        Storage::disk('public')->put($dummyPath, 'Isi file dummy');

        // 3. Update database dengan path tersebut
        $this->bimtek->update([
            'status' => 'persiapan',
            'file_surat_undangan_path' => $dummyPath,
            // ... atribut lain yang dibutuhkan ...
        ]);
        
        // Pastikan $this->bimtek dari setUp() sudah memiliki PIC aktif, Panitia,
        // Kuota, Lokasi, Tgl Mulai/Selesai valid, Surat Undangan, dan Mode Pelaksanaan.
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_not_in_persiapan_status(): void
    {
        $this->bimtek->update(['status' => 'registrasi']);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Bimtek harus berada dalam fase Persiapan untuk dapat membuka registrasi.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_pic_missing_or_inactive(): void
    {
        // Simulasi PIC tidak aktif (asumsi relasi pic bisa dimodifikasi)
        $this->bimtek->pic->update(['is_active' => false]);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('PIC kegiatan belum ditetapkan, atau akun PIC yang bersangkutan saat ini tidak aktif.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_no_panitia_assigned(): void
    {
        // Hapus semua panitia yang mungkin dibuat oleh factory
        $this->bimtek->panitia()->detach();
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Minimal satu orang panitia harus ditambahkan ke dalam kegiatan ini.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_judul_rencana_empty(): void
    {
        $this->bimtek->update(['judul_rencana' => null]);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Judul rencana kegiatan belum diisi.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_kuota_peserta_invalid(): void
    {
        $this->bimtek->update(['jumlah_peserta' => 0]);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Kuota peserta wajib diisi dengan angka lebih dari nol.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_tanggal_rencana_incomplete(): void
    {
        $this->bimtek->update(['tanggal_mulai_rencana' => null]);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Tanggal mulai dan selesai rencana pelaksanaan belum lengkap.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_tanggal_selesai_is_before_mulai(): void
    {
        $this->bimtek->update([
            'tanggal_mulai_rencana' => now()->addDays(5),
            'tanggal_selesai_rencana' => now()->addDays(2),
        ]);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Logika jadwal keliru: Tanggal selesai tidak boleh lebih awal dari tanggal mulai.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_surat_undangan_missing(): void
    {
        $this->bimtek->update(['file_surat_undangan_path' => null]);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Dokumen surat undangan resmi belum diunggah.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_online_but_no_url(): void
    {
        $this->bimtek->update([
            'mode_pelaksanaan' => 'online',
            'virtual_meeting_url' => null
        ]);
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Pelaksanaan berstatus Online/Hybrid mewajibkan ketersediaan tautan (URL) virtual meeting.', $check['errors']);
    }

    #[Test]
    public function cannot_open_registration_if_verifikasi_aktif_but_no_syarat_dokumen(): void
    {
        // Asumsi tidak ada dokumen yang direlasikan
        $this->bimtek->update(['butuh_verifikasi_dokumen' => true]);
        $this->bimtek->syaratDokumens()->delete();
        
        $check = $this->bimtek->canOpenRegistration();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Fitur verifikasi dokumen diaktifkan, namun belum ada jenis dokumen persyaratan yang ditambahkan.', $check['errors']);
    }

    // ==================== TESTS FOR canCompletePreparation() ====================

    #[Test]
    public function can_complete_preparation_when_all_requirements_met(): void
    {
        // 1. Siapkan data dasar yang valid
        $this->bimtek->update([
            'status' => 'registrasi',
            'has_tugas' => false,
            'butuh_verifikasi_dokumen' => false,
            'tanggal_mulai_aktual' => now()->addDays(5),
            'tanggal_selesai_aktual' => now()->addDays(6),
            'lokasi_aktual' => 'Ruang Aula Utama',
            'mode_pelaksanaan' => 'offline',
        ]);
        
        // 2. Siapkan relasi wajib secara manual (tanpa factory khusus)
        $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $this->bimtek->peserta()->attach($peserta->id, ['status_verifikasi' => 'verified']);
        
        // Buat manual menggunakan model langsung
        BimtekPemateri::create([
            'bimtek_id' => $this->bimtek->id,
            'nama_pemateri' => 'Dr. Budi, M.Kom', // Sesuaikan kolom tabel pemateri Anda jika berbeda
            'asal_instansi' => 'Universitas Andalas',
        ]);
        
        Materi::create([
            'bimtek_id' => $this->bimtek->id,
            'judul' => 'Modul Dasar Sistem', // Sesuaikan kolom tabel materi Anda jika berbeda
            'file_path' => 'uploads/materi/dummy.pdf',
            'tipe' => 'materi',
        ]);
        
        // Eksekusi
        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_not_in_registrasi_status(): void
    {
        $this->bimtek->update(['status' => 'persiapan']);
        
        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Bimtek harus berada dalam fase Registrasi untuk dapat diselesaikan persiapannya.', $check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_no_panitia_left(): void
    {
        $this->bimtek->update(['status' => 'registrasi']);
        $this->bimtek->panitia()->detach(); // Hapus panitia dari setUp
        
        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Minimal satu orang panitia harus tetap terdaftar dalam kegiatan ini.', $check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_zero_peserta(): void
    {
        $this->bimtek->update(['status' => 'registrasi']);
        // Tidak meng-attach peserta sama sekali
        
        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Belum ada peserta yang mendaftar. Kelas tidak dapat dilanjutkan ke tahap persiapan selesai tanpa peserta.', $check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_peserta_verification_is_pending(): void
    {
        $this->bimtek->update([
            'status' => 'registrasi',
            'butuh_verifikasi_dokumen' => true,
        ]);

        $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        
        // PERBAIKAN 1: Ubah 'menunggu' menjadi 'pending'
        $this->bimtek->peserta()->attach($peserta->id, ['status_verifikasi' => 'pending']);
        
        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Masih terdapat dokumen peserta yang belum diperiksa panitia. Loloskan atau tolak sisa pendaftar terlebih dahulu.', $check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_tugas_required_but_missing(): void
    {
        $this->bimtek->update([
            'status' => 'registrasi',
            'has_tugas' => true, // Syarat tugas aktif
        ]);
        
        // Inject 1 peserta agar lolos validasi peserta
        $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $this->bimtek->peserta()->attach($peserta->id);

        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Fitur tugas kelas diaktifkan, namun draf lembar tugas belum dibuat ke dalam sistem.', $check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_pemateri_or_materi_missing(): void
    {
        $this->bimtek->update(['status' => 'registrasi']);
       $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $this->bimtek->peserta()->attach($peserta->id);
        
        // Kosongkan pemateri dan materi secara manual jika ada sisa dari test lain
        $this->bimtek->pemateris()->delete();
        $this->bimtek->materis()->delete();

        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Daftar narasumber/pemateri belum ditambahkan.', $check['errors']);
        $this->assertContains('Modul materi kegiatan belum diunggah.', $check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_tanggal_aktual_missing(): void
    {
        $this->bimtek->update([
            'status' => 'registrasi',
            'tanggal_mulai_aktual' => null,
            'tanggal_selesai_aktual' => null,
        ]);
        $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $this->bimtek->peserta()->attach($peserta->id);
        
        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Jadwal aktual pelaksanaan (tanggal mulai & selesai) wajib ditetapkan secara final sebelum pendaftaran ditutup.', $check['errors']);
    }

    #[Test]
    public function cannot_complete_preparation_if_logistik_requested_but_not_approved_by_rt(): void
    {
        $this->bimtek->update([
            'status' => 'registrasi',
            'status_rt' => 'belum_dipenuhi', // Belum disetujui RT
        ]);
        
        $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $this->bimtek->peserta()->attach($peserta->id);
        
        // Buat data logistik secara manual
        FasilitasLogistik::create([
            'bimtek_id' => $this->bimtek->id,
            'nama_fasilitas' => 'Proyektor', // Sesuaikan kolom tabel fasilitas_logistiks Anda jika berbeda
        ]);
        
        $check = $this->bimtek->canCompletePreparation();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Terdapat permohonan fasilitas/logistik yang diajukan, namun belum disetujui atau dipenuhi oleh Koordinator RT.', $check['errors']);
    }

    #[Test]
    public function cannot_finish_event_if_tugas_not_fully_graded(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => now()->subHours(2),
            'has_tugas' => true,
        ]);

        // Gunakan forceCreate untuk memastikan data masuk mengabaikan $fillable
        $tugas = Tugas::forceCreate([
            'bimtek_id' => $this->bimtek->id,
            'judul' => 'Tugas Praktik',
        ]);

        $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        
        // PERBAIKAN 2: Daftarkan peserta ke kelas Bimtek terlebih dahulu agar Foreign Key valid
        $this->bimtek->peserta()->attach($peserta->id);

        PengumpulanTugas::forceCreate([
            'tugas_id' => $tugas->id,
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $peserta->id,
            'nilai' => null, 
        ]);

        $check = $this->bimtek->canFinishEvent();

        $this->assertFalse($check['allowed']);
        $this->assertContains('Fitur tugas aktif, namun masih terdapat hasil pengumpulan tugas peserta yang belum dinilai.', $check['errors']);
    }

    // ==================== TESTS FOR canStartEvent() ====================

    #[Test]
    public function can_start_event_when_all_requirements_met(): void
    {
        // 1. Siapkan data dasar yang valid (waktu mulai sudah lewat/hari ini)
        $this->bimtek->update([
            'status' => 'persiapan_selesai',
            'tanggal_mulai_aktual' => now()->subHours(1), // Sudah tiba (past date)
            'tanggal_selesai_aktual' => now()->addDays(2),
            'mode_pelaksanaan' => 'offline',
        ]);

        // 2. Buat relasi wajib secara manual
        SesiAbsensi::create([
            'bimtek_id' => $this->bimtek->id,
            'nama_sesi' => 'Sesi Pembukaan',
            'status' => 'terbuka', // Wajib terbuka sesuai syarat baru
        ]);

        BimtekPemateri::create([
            'bimtek_id' => $this->bimtek->id,
            'nama_pemateri' => 'Dr. Budi, M.Kom',
            'asal_instansi' => 'Universitas Andalas',
        ]);
        
        $check = $this->bimtek->canStartEvent();
        
        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_not_in_persiapan_selesai_status(): void
    {
        $this->bimtek->update(['status' => 'registrasi']);
        
        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Persiapan Bimtek belum dinyatakan selesai.', $check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_tanggal_aktual_empty(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan_selesai',
            'tanggal_mulai_aktual' => null,
            'tanggal_selesai_aktual' => null,
        ]);
        
        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Tanggal mulai dan selesai aktual belum ditetapkan.', $check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_tanggal_mulai_greater_than_selesai(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan_selesai',
            'tanggal_mulai_aktual' => now()->addDays(3),
            'tanggal_selesai_aktual' => now()->addDays(1), // Selesai lebih cepat dari mulai
        ]);
        
        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Tanggal mulai tidak boleh lebih besar dari tanggal selesai.', $check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_tanggal_mulai_in_future(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan_selesai',
            'tanggal_mulai_aktual' => now()->addDays(1), // Belum tiba (future date)
            'tanggal_selesai_aktual' => now()->addDays(2),
        ]);
        
        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Tanggal mulai aktual belum tiba.', $check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_no_open_sesi_absensi(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan_selesai',
            'tanggal_mulai_aktual' => now()->subHours(1),
            'tanggal_selesai_aktual' => now()->addDays(2),
        ]);

        // Sesi dibuat tapi statusnya 'ditutup'
        SesiAbsensi::create([
            'bimtek_id' => $this->bimtek->id,
            'nama_sesi' => 'Sesi Tutup',
            'status' => 'ditutup',
        ]);
        
        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Minimal harus ada satu sesi absensi dengan status terbuka sebelum kegiatan dimulai.', $check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_no_pemateri(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan_selesai',
            'tanggal_mulai_aktual' => now()->subHours(1),
            'tanggal_selesai_aktual' => now()->addDays(2),
        ]);

        SesiAbsensi::create([
            'bimtek_id' => $this->bimtek->id,
            'nama_sesi' => 'Sesi 1',
            'status' => 'terbuka',
        ]);
        
        // Sengaja tidak membuat data BimtekPemateri

        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Daftar narasumber/pemateri final belum tersedia.', $check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_no_panitia_left(): void
    {
        $this->bimtek->update(['status' => 'persiapan_selesai']);
        
        // Hapus panitia yang di-attach dari setUp()
        $this->bimtek->panitia()->detach();
        
        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Tidak ada panitia yang terdaftar untuk mengawal kegiatan ini.', $check['errors']);
    }

    #[Test]
    public function cannot_start_event_if_online_and_url_empty(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan_selesai',
            'tanggal_mulai_aktual' => now()->subHours(1),
            'tanggal_selesai_aktual' => now()->addDays(2),
            'mode_pelaksanaan' => 'online', // Diubah menjadi online
            'virtual_meeting_url' => null, // Tapi URL kosong
        ]);

        $check = $this->bimtek->canStartEvent();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Tautan (URL) virtual meeting wajib diisi untuk pelaksanaan berstatus Online atau Hybrid.', $check['errors']);
    }

    // ==================== TESTS FOR canFinishEvent() ====================

    #[Test]
    public function can_finish_event_when_all_requirements_met(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => now()->subHours(2), // Sudah berlalu
            'has_tugas' => false,
            'has_sertifikat' => false,
        ]);

        $check = $this->bimtek->canFinishEvent();

        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    #[Test]
    public function cannot_finish_event_if_not_in_berlangsung_status(): void
    {
        $this->bimtek->update(['status' => 'persiapan_selesai']);

        $check = $this->bimtek->canFinishEvent();

        $this->assertFalse($check['allowed']);
        $this->assertContains('Bimtek harus berstatus Berlangsung untuk dapat diakhiri.', $check['errors']);
    }

    #[Test]
    public function cannot_finish_event_if_tanggal_selesai_empty(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => null,
        ]);

        $check = $this->bimtek->canFinishEvent();

        $this->assertFalse($check['allowed']);
        $this->assertContains('Tanggal selesai aktual belum ditetapkan.', $check['errors']);
    }

    #[Test]
    public function cannot_finish_event_if_tanggal_selesai_in_future(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => now()->addDays(1), // Belum berlalu
        ]);

        $check = $this->bimtek->canFinishEvent();

        $this->assertFalse($check['allowed']);
        $this->assertContains('Kegiatan belum bisa diakhiri karena tanggal selesai aktual belum terlewati.', $check['errors']);
    }

    #[Test]
    public function cannot_finish_event_if_sesi_absensi_still_open(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => now()->subHours(2),
        ]);

        SesiAbsensi::create([
            'bimtek_id' => $this->bimtek->id,
            'nama_sesi' => 'Sesi Penutupan',
            'status' => 'terbuka',
        ]);

        $check = $this->bimtek->canFinishEvent();

        $this->assertFalse($check['allowed']);
        $this->assertContains('Masih ada sesi absensi yang berstatus terbuka. Harap tutup semua sesi absensi terlebih dahulu.', $check['errors']);
    }

    #[Test]
    public function cannot_finish_event_if_sertifikat_not_generated(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => now()->subHours(2),
            'has_tugas' => false,
            'has_sertifikat' => true, // Sertifikat aktif
        ]);

        // TAMBAHAN WAJIB: Masukkan minimal 1 peserta terverifikasi
        $peserta = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $this->bimtek->peserta()->attach($peserta->id, ['status_verifikasi' => 'verified']);
        // Tidak perlu membuat data sertifikat, biarkan kosong agar memicu error

        $check = $this->bimtek->canFinishEvent();

        $this->assertFalse($check['allowed']);
        
        // Pastikan teks string ini sama PERSIS karakternya dengan yang Anda tulis di model Bimtek.php
        $this->assertContains('Fitur sertifikat diaktifkan, namun belum ada sertifikat yang diterbitkan. Harap proses generate kelulusan/sertifikat terlebih dahulu.', $check['errors']);
    }

    #[Test]
    public function cannot_finish_event_if_not_all_sertifikats_processed(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => now()->subHours(2),
            'has_sertifikat' => true,
            'has_tugas' => false, 
        ]);

        // Buat 2 peserta dan verifikasi keduanya
        $peserta1 = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $peserta2 = User::factory()->create(['role_id' => $this->roleInternal->id]);
        
        $this->bimtek->peserta()->attach([
            $peserta1->id => ['status_verifikasi' => 'verified'],
            $peserta2->id => ['status_verifikasi' => 'verified'],
        ]);

        // HANYA cetak 1 sertifikat menggunakan DB facade dan lengkapi kolom yang wajib (NOT NULL)
        \Illuminate\Support\Facades\DB::table('sertifikats')->insert([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $peserta1->id,
            'nomor_sertifikat' => 'SRT/001/' . date('Y'),
            'tanggal_terbit' => now()->toDateString(),
            'file_path' => 'sertifikat/dummy-001.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $check = $this->bimtek->canFinishEvent();

        $this->assertFalse($check['allowed']);
        $this->assertContains('Masih terdapat 1 peserta terverifikasi yang belum diproses sertifikatnya.', $check['errors']);
    }

    #[Test]
    public function can_finish_event_even_if_some_participants_did_not_submit_task(): void
    {
        $this->bimtek->update([
            'status' => 'berlangsung',
            'tanggal_selesai_aktual' => now()->subHours(2),
            'has_tugas' => true,
        ]);

        $tugas = Tugas::forceCreate([
            'bimtek_id' => $this->bimtek->id,
            'judul' => 'Laporan Praktik',
        ]);

        // Buat 2 peserta terverifikasi
        $pesertaRajin = User::factory()->create(['role_id' => $this->roleInternal->id]);
        $pesertaSakit = User::factory()->create(['role_id' => $this->roleInternal->id]);
        
        $this->bimtek->peserta()->attach([
            $pesertaRajin->id => ['status_verifikasi' => 'verified'],
            $pesertaSakit->id => ['status_verifikasi' => 'verified'],
        ]);

        // HANYA peserta 1 yang mengumpulkan DAN sudah dinilai panitia
        PengumpulanTugas::forceCreate([
            'tugas_id' => $tugas->id,
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $pesertaRajin->id,
            'nilai' => 85, 
        ]);
        // Peserta 2 (Sakit) dibiarkan kosong, tidak ada record di tabel pengumpulan_tugas

        $check = $this->bimtek->canFinishEvent();

        // Sistem SEHARUSNYA mengizinkan kelas ditutup
        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    // ==================== TESTS FOR canCancel() ====================

    #[Test]
    public function can_cancel_from_persiapan_status(): void
    {
        $this->bimtek->update(['status' => 'persiapan_selesai']);
        
        $check = $this->bimtek->canCancel();
        
        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    #[Test]
    public function can_cancel_from_registrasi_status(): void
    {
        $this->bimtek->update(['status' => 'registrasi']);
        
        $check = $this->bimtek->canCancel();
        
        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    #[Test]
    public function can_cancel_from_berlangsung_status(): void
    {
        $this->bimtek->update(['status' => 'berlangsung']);
        
        $check = $this->bimtek->canCancel();
        
        $this->assertTrue($check['allowed']);
        $this->assertEmpty($check['errors']);
    }

    #[Test]
    public function cannot_cancel_if_already_selesai(): void
    {
        $this->bimtek->update(['status' => 'selesai']);
        
        $check = $this->bimtek->canCancel();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Kegiatan yang sudah diselesaikan secara penuh tidak dapat dibatalkan.', $check['errors']);
    }

    #[Test]
    public function cannot_cancel_if_already_dibatalkan(): void
    {
        $this->bimtek->update(['status' => 'dibatalkan']);
        
        $check = $this->bimtek->canCancel();
        
        $this->assertFalse($check['allowed']);
        $this->assertContains('Kegiatan ini sudah berstatus dibatalkan.', $check['errors']);
    }
}