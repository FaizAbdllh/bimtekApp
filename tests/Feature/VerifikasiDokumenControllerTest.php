<?php

namespace Tests\Feature;

use App\Mail\DokumenVerifiedRejectedMail;
use App\Mail\PesertaBimtekInvitedMail;
use App\Models\Bimtek;
use App\Models\DokumenPersyaratanPeserta;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerifikasiDokumenControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;
    protected User $panitia;
    protected User $peserta;
    protected Bimtek $bimtek;
    protected Role $roleAdmin;
    protected Role $rolePeserta;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->roleAdmin = Role::create(['nama_peran' => 'Admin IT']);
        $this->rolePeserta = Role::create(['nama_peran' => 'Peserta Eksternal']);

        // Create users
        $this->pic = User::factory()->create(['role_id' => $this->roleAdmin->id]);
        $this->panitia = User::factory()->create(['role_id' => $this->roleAdmin->id]);
        $this->peserta = User::factory()->create(['role_id' => $this->rolePeserta->id]);

        // Create bimtek with verification enabled
        $this->bimtek = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'butuh_verifikasi_dokumen' => true,
            'jenis_dokumen_wajib' => ['surat_tugas', 'sppd'],
        ]);

        // Assign panitia and peserta
        $this->bimtek->users()->attach($this->panitia->id, [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'panitia',
        ]);
        $this->bimtek->users()->attach($this->peserta->id, [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'peserta',
            'status_verifikasi' => 'invited',
            'notified_at' => now(),
        ]);

        Storage::fake('public');
    }

    #[Test]
    public function peserta_can_access_upload_form()
    {
        $response = $this->actingAs($this->peserta)
            ->get(route('bimtek.verifikasi-dokumen.upload-form', $this->bimtek));

        $response->assertOk();
        $response->assertViewIs('verifikasi-dokumen.upload');
        $response->assertSee('Upload Dokumen Persyaratan');
    }

    #[Test]
    public function non_peserta_gets_403_on_upload_form()
    {
        $otherUser = User::factory()->create(['role_id' => $this->rolePeserta->id]);

        $response = $this->actingAs($otherUser)
            ->get(route('bimtek.verifikasi-dokumen.upload-form', $this->bimtek));

        $response->assertForbidden();
    }

    #[Test]
    public function peserta_can_upload_valid_document()
    {
        $file = UploadedFile::fake()->create('surat_tugas.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->peserta)
            ->post(route('bimtek.verifikasi-dokumen.upload', $this->bimtek), [
                'jenis_dokumen' => 'surat_tugas',
                'file' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check database
        $this->assertDatabaseHas('dokumen_persyaratan_peserta', [
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'status' => 'pending',
        ]);

        // Check file exists
        $dokumen = DokumenPersyaratanPeserta::first();
        Storage::disk('public')->assertExists($dokumen->file_path);

        // Check peserta status updated
        $this->assertDatabaseHas('bimtek_user', [
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'status_verifikasi' => 'pending',
        ]);
    }

    #[Test]
    public function upload_validates_file_type()
    {
        $file = UploadedFile::fake()->create('malware.exe', 100);

        $response = $this->actingAs($this->peserta)
            ->post(route('bimtek.verifikasi-dokumen.upload', $this->bimtek), [
                'jenis_dokumen' => 'surat_tugas',
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('dokumen_persyaratan_peserta', 0);
    }

    #[Test]
    public function upload_validates_file_size()
    {
        $file = UploadedFile::fake()->create('large.pdf', 3000, 'application/pdf'); // 3MB

        $response = $this->actingAs($this->peserta)
            ->post(route('bimtek.verifikasi-dokumen.upload', $this->bimtek), [
                'jenis_dokumen' => 'surat_tugas',
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('dokumen_persyaratan_peserta', 0);
    }

    #[Test]
    public function pic_can_access_verification_dashboard()
    {
        $response = $this->actingAs($this->pic)
            ->get(route('bimtek.verifikasi-dokumen.index', $this->bimtek));

        $response->assertOk();
        $response->assertViewIs('verifikasi-dokumen.index');
    }

    #[Test]
    public function panitia_can_access_verification_dashboard()
    {
        $response = $this->actingAs($this->panitia)
            ->get(route('bimtek.verifikasi-dokumen.index', $this->bimtek));

        $response->assertOk();
        $response->assertViewIs('verifikasi-dokumen.index');
    }

    #[Test]
    public function peserta_cannot_access_verification_dashboard()
    {
        $response = $this->actingAs($this->peserta)
            ->get(route('bimtek.verifikasi-dokumen.index', $this->bimtek));

        $response->assertForbidden();
    }

    #[Test]
    public function pic_can_approve_document()
    {
        Mail::fake();

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/surat_tugas.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.verifikasi-dokumen.approve', $dokumen), [
                'catatan_verifikasi' => 'Dokumen sudah sesuai',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $dokumen->refresh();
        $this->assertEquals('approved', $dokumen->status);
        $this->assertEquals($this->pic->id, $dokumen->verified_by);
        $this->assertNotNull($dokumen->verified_at);
        $this->assertEquals('Dokumen sudah sesuai', $dokumen->catatan_verifikasi);
    }

    #[Test]
    public function panitia_can_approve_document()
    {
        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/surat_tugas.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.verifikasi-dokumen.approve', $dokumen));

        $response->assertRedirect();
        $dokumen->refresh();
        $this->assertEquals('approved', $dokumen->status);
    }

    #[Test]
    public function approve_all_documents_sets_peserta_verified()
    {
        Mail::fake();

        // Create 2 documents, 1 already approved
        DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/surat_tugas.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'approved',
            'uploaded_at' => now(),
        ]);

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'sppd',
            'file_path' => 'test/sppd.pdf',
            'file_name' => 'sppd.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->pic)
            ->post(route('bimtek.verifikasi-dokumen.approve', $dokumen));

        // Check peserta status
        $this->assertDatabaseHas('bimtek_user', [
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'status_verifikasi' => 'verified',
        ]);

        // Check email sent
        Mail::assertSent(DokumenVerifiedRejectedMail::class, function ($mail) {
            return $mail->status === 'verified';
        });
    }

    #[Test]
    public function pic_can_reject_document_with_catatan()
    {
        Mail::fake();

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/surat_tugas.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.verifikasi-dokumen.reject', $dokumen), [
                'catatan_verifikasi' => 'Format tidak sesuai',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $dokumen->refresh();
        $this->assertEquals('rejected', $dokumen->status);
        $this->assertEquals('Format tidak sesuai', $dokumen->catatan_verifikasi);

        // Check peserta status
        $this->assertDatabaseHas('bimtek_user', [
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'status_verifikasi' => 'rejected',
        ]);

        // Check email sent
        Mail::assertSent(DokumenVerifiedRejectedMail::class, function ($mail) {
            return $mail->status === 'rejected' && $mail->catatan === 'Format tidak sesuai';
        });
    }

    #[Test]
    public function reject_requires_catatan()
    {
        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/surat_tugas.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.verifikasi-dokumen.reject', $dokumen), [
                'catatan_verifikasi' => '', // Empty catatan
            ]);

        $response->assertSessionHasErrors('catatan_verifikasi');
        
        $dokumen->refresh();
        $this->assertEquals('pending', $dokumen->status); // Status unchanged
    }

    #[Test]
    public function peserta_can_download_own_document()
    {
        Storage::disk('public')->put('test/doc.pdf', 'test content');

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/doc.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->peserta)
            ->get(route('bimtek.verifikasi-dokumen.download', $dokumen));

        $response->assertOk();
        $response->assertDownload('surat_tugas.pdf');
    }

    #[Test]
    public function peserta_cannot_download_others_document()
    {
        $otherPeserta = User::factory()->create(['role_id' => $this->rolePeserta->id]);

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $otherPeserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/doc.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->peserta)
            ->get(route('bimtek.verifikasi-dokumen.download', $dokumen));

        $response->assertForbidden();
    }

    #[Test]
    public function pic_can_download_any_document()
    {
        Storage::disk('public')->put('test/doc.pdf', 'test content');

        $dokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/doc.pdf',
            'file_name' => 'surat_tugas.pdf',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->pic)
            ->get(route('bimtek.verifikasi-dokumen.download', $dokumen));

        $response->assertOk();
    }

    #[Test]
    public function peserta_can_reupload_rejected_document()
    {
        // Create rejected document
        $oldDokumen = DokumenPersyaratanPeserta::create([
            'bimtek_id' => $this->bimtek->id,
            'user_id' => $this->peserta->id,
            'jenis_dokumen' => 'surat_tugas',
            'file_path' => 'test/old.pdf',
            'file_name' => 'old.pdf',
            'status' => 'rejected',
            'uploaded_at' => now(),
        ]);

        Storage::disk('public')->put($oldDokumen->file_path, 'old content');

        // Upload new file
        $newFile = UploadedFile::fake()->create('new_surat_tugas.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->peserta)
            ->post(route('bimtek.verifikasi-dokumen.upload', $this->bimtek), [
                'jenis_dokumen' => 'surat_tugas',
                'file' => $newFile,
            ]);

        $response->assertRedirect();

        // Old file should be deleted
        Storage::disk('public')->assertMissing($oldDokumen->file_path);

        // New document should exist
        $newDokumen = DokumenPersyaratanPeserta::where('jenis_dokumen', 'surat_tugas')
            ->where('user_id', $this->peserta->id)
            ->latest()
            ->first();

        $this->assertEquals('pending', $newDokumen->status);
        Storage::disk('public')->assertExists($newDokumen->file_path);
    }
}
