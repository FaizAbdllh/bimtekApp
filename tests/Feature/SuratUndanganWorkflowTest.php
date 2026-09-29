<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuratUndanganWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;
    protected User $panitia;
    protected Bimtek $bimtek;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $roleInternal = Role::create(['nama_peran' => 'Pegawai Internal']);
        $this->pic = User::factory()->create(['role_id' => $roleInternal->id]);
        $this->panitia = User::factory()->create(['role_id' => $roleInternal->id]);

        $this->bimtek = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'status' => 'persiapan',
            'file_surat_undangan_path' => null,
            'file_surat_undangan_uploaded_by' => null,
            'file_surat_undangan_uploaded_at' => null,
        ]);

        $this->bimtek->panitia()->attach($this->panitia->id, [
            'fungsi_panitia' => 'Koordinator',
        ]);
    }

    #[Test]
    public function pic_can_upload_surat_undangan(): void
    {
        $response = $this->actingAs($this->pic)->post(
            route('bimtek.upload-draft', $this->bimtek),
            ['surat_draft' => UploadedFile::fake()->create('undangan.pdf', 100, 'application/pdf')]
        );

        $response->assertRedirect()->assertSessionHas('success');
        $bimtek = $this->bimtek->fresh();

        $this->assertNotNull($bimtek->file_surat_undangan_path);
        $this->assertSame($this->pic->id, $bimtek->file_surat_undangan_uploaded_by);
        $this->assertNotNull($bimtek->file_surat_undangan_uploaded_at);
        Storage::disk('public')->assertExists($bimtek->file_surat_undangan_path);
    }

    #[Test]
    public function panitia_can_upload_surat_undangan(): void
    {
        $response = $this->actingAs($this->panitia)->post(
            route('bimtek.upload-draft', $this->bimtek),
            ['surat_draft' => UploadedFile::fake()->create('undangan.pdf', 100, 'application/pdf')]
        );

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'file_surat_undangan_uploaded_by' => $this->panitia->id,
        ]);
    }

    #[Test]
    public function pic_and_panitia_can_replace_the_same_invitation_file(): void
    {
        $this->actingAs($this->pic)->post(route('bimtek.upload-draft', $this->bimtek), [
            'surat_draft' => UploadedFile::fake()->create('old.pdf', 100, 'application/pdf'),
        ]);

        $oldPath = $this->bimtek->fresh()->file_surat_undangan_path;

        $response = $this->actingAs($this->panitia)->post(route('bimtek.upload-final', $this->bimtek), [
            'surat_draft' => UploadedFile::fake()->create('new.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect()->assertSessionHas('success');
        $bimtek = $this->bimtek->fresh();

        $this->assertNotSame($oldPath, $bimtek->file_surat_undangan_path);
        $this->assertSame($this->panitia->id, $bimtek->file_surat_undangan_uploaded_by);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($bimtek->file_surat_undangan_path);
    }

    #[Test]
    public function invitation_can_be_previewed_and_downloaded(): void
    {
        $this->actingAs($this->pic)->post(route('bimtek.upload-draft', $this->bimtek), [
            'surat_draft' => UploadedFile::fake()->create('undangan.pdf', 100, 'application/pdf'),
        ]);

        $this->actingAs($this->pic)
            ->get(route('bimtek.preview-draft', $this->bimtek))
            ->assertOk();

        $this->actingAs($this->panitia)
            ->get(route('bimtek.download-draft', $this->bimtek))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    #[Test]
    public function invitation_uploader_relation_works(): void
    {
        $this->actingAs($this->pic)->post(route('bimtek.upload-draft', $this->bimtek), [
            'surat_draft' => UploadedFile::fake()->create('undangan.pdf', 100, 'application/pdf'),
        ]);

        $this->assertSame($this->pic->id, $this->bimtek->fresh()->undanganUploader->id);
    }
}