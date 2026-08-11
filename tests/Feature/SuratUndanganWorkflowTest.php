<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuratUndanganWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;

    protected User $persuratan;

    protected User $panitia;

    protected Bimtek $bimtek;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup storage
        Storage::fake('public');

        // Create roles
        $rolePegawaiInternal = Role::create(['nama_peran' => 'Pegawai Internal']);
        $rolePersuratan = Role::create(['nama_peran' => 'Persuratan']);

        // Create test users
        $this->pic = User::factory()->create([
            'name' => 'PIC Test',
            'email' => 'pic@test.com',
            'role_id' => $rolePegawaiInternal->id,
        ]);

        $this->panitia = User::factory()->create([
            'name' => 'Panitia Test',
            'email' => 'panitia@test.com',
            'role_id' => $rolePegawaiInternal->id,
        ]);

        $this->persuratan = User::factory()->create([
            'name' => 'Bagian Persuratan',
            'email' => 'persuratan@test.com',
            'role_id' => $rolePersuratan->id,
        ]);

        // Create bimtek
        $this->bimtek = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'status_pelaksanaan' => 'persiapan',
            'file_surat_draft_path' => null,
            'file_surat_draft_uploaded_by' => null,
            'file_surat_draft_uploaded_at' => null,
            'file_surat_final_path' => null,
            'file_surat_final_uploaded_by' => null,
            'file_surat_final_uploaded_at' => null,
        ]);

        // Attach panitia to bimtek
        $this->bimtek->users()->attach($this->panitia->id, [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'panitia',
        ]);

        // Attach persuratan to bimtek as 'peserta' so they have access to preview/download
        $this->bimtek->users()->attach($this->persuratan->id, [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'peserta',
        ]);

        // Attach PIC to bimtek pivot so PIC has access via authorizeAccess
        $this->bimtek->users()->attach($this->pic->id, [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'pic',
        ]);

    }

    #[Test]
    public function pic_can_upload_surat_draft(): void
    {
        $file = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify database
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'file_surat_draft_uploaded_by' => $this->pic->id,
        ]);

        // Verify file exists
        $bimtek = Bimtek::find($this->bimtek->id);
        $this->assertNotNull($bimtek->file_surat_draft_path);
        $this->assertEquals($this->pic->id, $bimtek->file_surat_draft_uploaded_by);
        $this->assertNotNull($bimtek->file_surat_draft_uploaded_at);
    }

    #[Test]
    public function panitia_can_upload_surat_draft(): void
    {
        $file = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify database
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'file_surat_draft_uploaded_by' => $this->panitia->id,
        ]);
    }

    #[Test]
    public function persuratan_only_can_upload_surat_final(): void
    {
        // First upload draft as PIC
        $draftFile = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $draftFile,
            ]);

        // Now upload final as Persuratan - should succeed
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->persuratan)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify database
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'file_surat_final_uploaded_by' => $this->persuratan->id,
        ]);
    }

    #[Test]
    public function non_persuratan_cannot_upload_surat_final(): void
    {
        // First upload draft as PIC
        $draftFile = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $draftFile,
            ]);

        // Try to upload final as PIC - should fail with 403
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);

        $response->assertForbidden();

        // Verify final file NOT uploaded
        $bimtek = Bimtek::find($this->bimtek->id);
        $this->assertNull($bimtek->file_surat_final_path);
        $this->assertNull($bimtek->file_surat_final_uploaded_by);
    }

    #[Test]
    public function panitia_cannot_upload_surat_final(): void
    {
        // First upload draft as PIC
        $draftFile = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $draftFile,
            ]);

        // Try to upload final as Panitia - should fail with 403
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);

        $response->assertForbidden();

        // Verify final file NOT uploaded
        $bimtek = Bimtek::find($this->bimtek->id);
        $this->assertNull($bimtek->file_surat_final_path);
        $this->assertNull($bimtek->file_surat_final_uploaded_by);
    }

    #[Test]
    public function can_preview_surat_draft(): void
    {
        // First upload draft
        $file = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $file,
            ]);

        // Preview as any user
        $response = $this->actingAs($this->persuratan)
            ->get(route('bimtek.preview-draft', $this->bimtek));

        $response->assertOk();
    }

    #[Test]
    public function can_preview_surat_final(): void
    {
        // First upload draft
        $draftFile = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $draftFile,
            ]);

        // Upload final as Persuratan
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');
        $this->actingAs($this->persuratan)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);

        // Preview as any user
        $response = $this->actingAs($this->pic)
            ->get(route('bimtek.preview-final', $this->bimtek));

        $response->assertOk();
    }

    #[Test]
    public function can_download_surat_draft(): void
    {
        // First upload draft
        $file = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $file,
            ]);

        // Download as any user
        $response = $this->actingAs($this->persuratan)
            ->get(route('bimtek.download-draft', $this->bimtek));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    #[Test]
    public function can_download_surat_final(): void
    {
        // First upload draft
        $draftFile = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $draftFile,
            ]);

        // Upload final as Persuratan
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');
        $this->actingAs($this->persuratan)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);

        // Download as any user
        $response = $this->actingAs($this->pic)
            ->get(route('bimtek.download-final', $this->bimtek));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    #[Test]
    public function draft_uploader_relation_works(): void
    {
        // Upload draft
        $file = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $file,
            ]);

        // Verify relation
        $bimtek = Bimtek::find($this->bimtek->id);
        $this->assertNotNull($bimtek->draftUploader);
        $this->assertEquals($this->pic->id, $bimtek->draftUploader->id);
    }

    #[Test]
    public function final_uploader_relation_works(): void
    {
        // Upload draft
        $draftFile = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $draftFile,
            ]);

        // Upload final
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');
        $this->actingAs($this->persuratan)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);

        // Verify relation
        $bimtek = Bimtek::find($this->bimtek->id);
        $this->assertNotNull($bimtek->finalUploader);
        $this->assertEquals($this->persuratan->id, $bimtek->finalUploader->id);
    }

    #[Test]
    public function is_persuratan_helper_works(): void
    {
        // PIC should not be persuratan
        $this->assertFalse($this->pic->isPersuratan());

        // Persuratan should be persuratan
        $this->assertTrue($this->persuratan->isPersuratan());
    }

    #[Test]
    public function complete_workflow_from_draft_to_final(): void
    {
        // Step 1: PIC uploads draft
        $draftFile = UploadedFile::fake()->create('surat_draft.pdf', 100, 'application/pdf');
        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.upload-draft', $this->bimtek), [
                'surat_draft' => $draftFile,
            ]);
        $response->assertRedirect();

        // Verify draft in database
        $bimtek = Bimtek::find($this->bimtek->id);
        $this->assertNotNull($bimtek->file_surat_draft_path);
        $this->assertEquals($this->pic->id, $bimtek->file_surat_draft_uploaded_by);

        // Step 2: PIC cannot upload final
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');
        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);
        $response->assertForbidden();

        // Step 3: Persuratan uploads final
        $finalFile = UploadedFile::fake()->create('surat_final.pdf', 100, 'application/pdf');
        $response = $this->actingAs($this->persuratan)
            ->post(route('bimtek.upload-final', $this->bimtek), [
                'surat_final' => $finalFile,
            ]);
        $response->assertRedirect();

        // Verify final in database
        $bimtek = Bimtek::find($this->bimtek->id);
        $this->assertNotNull($bimtek->file_surat_final_path);
        $this->assertEquals($this->persuratan->id, $bimtek->file_surat_final_uploaded_by);

        // Step 4: Both can preview
        $response = $this->actingAs($this->pic)->get(route('bimtek.preview-draft', $this->bimtek));
        $response->assertOk();

        $response = $this->actingAs($this->persuratan)->get(route('bimtek.preview-final', $this->bimtek));
        $response->assertOk();

        // Step 5: Both can download
        $response = $this->actingAs($this->pic)->get(route('bimtek.download-draft', $this->bimtek));
        $response->assertOk();

        $response = $this->actingAs($this->persuratan)->get(route('bimtek.download-final', $this->bimtek));
        $response->assertOk();
    }
}
