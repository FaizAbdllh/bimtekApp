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

        $response->assertRedirect()
            ->assertSessionHas('error_validasi');

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
        $otherUser = $this->panitia;

        $this->bimtek->update(['status' => 'disetujui_final']);

        $this->actingAs($otherUser)
            ->post(route('bimtek.request-revisi', $this->bimtek))
            ->assertForbidden();

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'status' => 'disetujui_final',
        ]);
    }

    #[Test]
    public function online_bimtek_cannot_open_registration_without_virtual_link(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan',
            'mode_pelaksanaan' => 'online',
            'virtual_meeting_url' => null,
        ]);

        $result = $this->bimtek->canOpenRegistration();

        $this->assertFalse($result['allowed']);
        $this->assertStringContainsString(
            'tautan',
            strtolower(implode(' ', $result['errors']))
        );
    }
    
    #[Test]
    public function hybrid_bimtek_cannot_open_registration_without_virtual_link(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan',
            'mode_pelaksanaan' => 'hybrid',
            'virtual_meeting_url' => null,
        ]);

        $result = $this->bimtek->canOpenRegistration();

        $this->assertFalse($result['allowed']);
    }
    
    #[Test]
    public function offline_bimtek_does_not_require_virtual_link(): void
    {
        $this->bimtek->update([
            'status' => 'persiapan',
            'mode_pelaksanaan' => 'offline',
            'virtual_meeting_url' => null,
        ]);

        $result = $this->bimtek->canOpenRegistration();

        $this->assertNotContains(
            'Pelaksanaan berstatus Online/Hybrid mewajibkan ketersediaan tautan (URL) virtual meeting.',
            $result['errors']
        );
    }
    
    #[Test]
    public function pic_can_update_virtual_meeting_url(): void
    {
        $this->bimtek->update([
            'mode_pelaksanaan' => 'online',
            'status' => 'persiapan',
        ]);
        $response = $this->actingAs($this->pic)
            ->put(route('bimtek.update', $this->bimtek), [
                'judul_final' => 'Bimtek Test',
                'virtual_meeting_url' => 'https://zoom.us/j/123456',
                // field wajib lain
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'virtual_meeting_url' => 'https://zoom.us/j/123456',
        ]);
    }

    #[Test]
    public function pic_cannot_update_virtual_meeting_url_if_bimtek_is_in_registration(): void
    {
        $this->bimtek->update([
            'mode_pelaksanaan' => 'online',
            'status' => 'registrasi',
            'virtual_meeting_url' => 'https://zoom.us/j/old-link',
        ]);

        $this->actingAs($this->pic)
            ->put(route('bimtek.update', $this->bimtek), [
                'judul_final' => 'Bimtek Test',
                'virtual_meeting_url' => 'https://zoom.us/j/new-link',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('bimteks', [
            'id' => $this->bimtek->id,
            'virtual_meeting_url' => 'https://zoom.us/j/old-link',
        ]);
    }
}
