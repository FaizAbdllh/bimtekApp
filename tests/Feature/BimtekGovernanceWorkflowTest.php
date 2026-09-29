<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $role = Role::create(['nama_peran' => 'Pegawai Internal']);
        $this->pic = User::factory()->create(['role_id' => $role->id]);
        $this->panitia = User::factory()->create(['role_id' => $role->id]);
        $this->bimtek = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'status' => 'persiapan',
            'judul_rencana' => 'Bimtek Test',
            'tanggal_mulai_rencana' => now()->addDay(),
            'tanggal_selesai_rencana' => now()->addDays(2),
        ]);

        $this->bimtek->panitia()->attach($this->panitia->id, [
            'fungsi_panitia' => 'Koordinator',
        ]);
    }

    #[Test]
    public function panitia_cannot_skip_preparation_stages(): void
    {
        $response = $this->actingAs($this->panitia)->patch(
            route('bimtek.update-status', $this->bimtek),
            ['status' => 'berlangsung']
        );

        $response->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status' => 'persiapan',
        ]);
    }

    #[Test]
    public function panitia_cannot_cancel_bimtek(): void
    {
        $response = $this->actingAs($this->panitia)->patch(
            route('bimtek.update-status', $this->bimtek),
            ['status' => 'dibatalkan']
        );

        $response->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status' => 'persiapan',
        ]);
    }

    #[Test]
    public function invalid_status_is_rejected(): void
    {
        $response = $this->actingAs($this->panitia)->patch(
            route('bimtek.update-status', $this->bimtek),
            ['status' => 'ditolak']
        );

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status' => 'persiapan',
        ]);
    }

    #[Test]
    public function pic_can_request_revision_from_final_approval(): void
    {
        $this->bimtek->update(['status' => 'disetujui_final']);

        $response = $this->actingAs($this->pic)
            ->post(route('bimtek.request-revisi', $this->bimtek));

        $response->assertRedirect(route('pengajuan.edit', $this->bimtek->id));
        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status' => 'perlu_revisi',
        ]);
    }

    #[Test]
    public function non_pic_cannot_request_revision(): void
    {
        $otherUser = User::factory()->create();
        $this->bimtek->update(['status' => 'disetujui_final']);

        $this->actingAs($otherUser)
            ->post(route('bimtek.request-revisi', $this->bimtek))
            ->assertForbidden();

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status' => 'disetujui_final',
        ]);
    }
}
