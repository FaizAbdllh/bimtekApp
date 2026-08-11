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

class BimtekGovernanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;

    protected User $panitia;

    protected Bimtek $bimtek;

    protected function setUp(): void
    {
        parent::setUp();

        $roleInternal = Role::create(['nama_peran' => 'Pegawai Internal']);

        $this->pic = User::factory()->create(['role_id' => $roleInternal->id]);
        $this->panitia = User::factory()->create(['role_id' => $roleInternal->id]);

        $this->bimtek = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'status_pelaksanaan' => 'persiapan',
        ]);

        $this->bimtek->users()->attach($this->panitia->id, [
            'id' => (string) Str::uuid(),
            'peran_kontekstual' => 'panitia',
        ]);
    }

    #[Test]
    public function panitia_can_start_bimtek_from_persiapan_to_berlangsung(): void
    {
        $response = $this->actingAs($this->panitia)
            ->patch(route('bimtek.update-status', $this->bimtek), [
                'status_pelaksanaan' => 'berlangsung',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status_pelaksanaan' => 'berlangsung',
        ]);
    }

    #[Test]
    public function panitia_cannot_cancel_bimtek_directly(): void
    {
        $response = $this->actingAs($this->panitia)
            ->patch(route('bimtek.update-status', $this->bimtek), [
                'status_pelaksanaan' => 'dibatalkan',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status_pelaksanaan' => 'persiapan',
        ]);

        $this->assertDatabaseHas('log_sistems', [
            'level' => 'warning',
        ]);
    }

    #[Test]
    public function invalid_status_transition_is_rejected_and_logged(): void
    {
        $response = $this->actingAs($this->panitia)
            ->patch(route('bimtek.update-status', $this->bimtek), [
                'status_pelaksanaan' => 'selesai',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status_pelaksanaan' => 'persiapan',
        ]);

        $this->assertDatabaseHas('log_sistems', [
            'level' => 'warning',
        ]);
    }

    #[Test]
    public function upload_undangan_is_locked_when_status_is_not_persiapan(): void
    {
        Storage::fake('public');

        $this->bimtek->update(['status_pelaksanaan' => 'berlangsung']);

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.upload-undangan', $this->bimtek), [
                'surat_undangan' => UploadedFile::fake()->create('undangan.pdf', 100, 'application/pdf'),
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status_pelaksanaan' => 'berlangsung',
            'file_surat_undangan_path' => null,
        ]);

        $this->assertDatabaseHas('log_sistems', [
            'level' => 'warning',
        ]);
    }

    #[Test]
    public function pic_can_request_major_revision_and_pengajuan_returns_to_perlu_revisi(): void
    {
        $this->bimtek->pengajuan->update([
            'status_pengajuan' => 'disetujui_final',
            'catatan_kepala' => 'disetujui',
            'catatan_ppk' => 'disetujui',
            'kepala_approved_at' => now(),
        ]);

        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.request-revisi', $this->bimtek));

        $response->assertRedirect(route('pengajuan.edit', $this->bimtek->pengajuan));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengajuans', [
            'id' => $this->bimtek->pengajuan->id,
            'status_pengajuan' => 'perlu_revisi',
            'catatan_kepala' => null,
            'catatan_ppk' => null,
        ]);
    }

    #[Test]
    public function non_pic_cannot_request_major_revision(): void
    {
        $otherUser = User::factory()->create();

        $this->bimtek->pengajuan->update([
            'status_pengajuan' => 'disetujui_final',
            'kepala_approved_at' => now(),
        ]);

        $response = $this->actingAs($otherUser)
            ->post(route('bimtek.request-revisi', $this->bimtek));

        $response->assertForbidden();

        $this->assertDatabaseHas('pengajuans', [
            'id' => $this->bimtek->pengajuan->id,
            'status_pengajuan' => 'disetujui_final',
        ]);
    }
}
