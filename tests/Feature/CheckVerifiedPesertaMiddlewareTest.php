<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckVerifiedPesertaMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;

    protected User $panitia;

    protected User $pesertaVerified;

    protected User $pesertaInvited;

    protected User $pesertaPending;

    protected User $pesertaRejected;

    protected Bimtek $bimtekWithVerification;

    protected Bimtek $bimtekWithoutVerification;

    protected Role $roleAdmin;

    protected Role $rolePeserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleAdmin = Role::create(['nama_peran' => 'Admin IT']);
        $this->rolePeserta = Role::create(['nama_peran' => 'Peserta Eksternal']);

        $this->pic = User::factory()->create(['role_id' => $this->roleAdmin->id]);
        $this->panitia = User::factory()->create(['role_id' => $this->roleAdmin->id]);
        $this->pesertaVerified = User::factory()->create(['role_id' => $this->rolePeserta->id]);
        $this->pesertaInvited = User::factory()->create(['role_id' => $this->rolePeserta->id]);
        $this->pesertaPending = User::factory()->create(['role_id' => $this->rolePeserta->id]);
        $this->pesertaRejected = User::factory()->create(['role_id' => $this->rolePeserta->id]);

        // Bimtek WITH verification
        $this->bimtekWithVerification = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'butuh_verifikasi_dokumen' => true,
            'has_tugas' => true,
            'has_sertifikat' => true,
        ]);

        // Assign users with different statuses
        $this->bimtekWithVerification->panitia()->attach($this->panitia->id, [
            'fungsi_panitia' => 'Koordinator',
        ]);
        $this->bimtekWithVerification->peserta()->attach($this->pesertaVerified->id, [
            'status_verifikasi' => 'verified',
        ]);
        $this->bimtekWithVerification->peserta()->attach($this->pesertaInvited->id, [
            'status_verifikasi' => 'invited',
        ]);
        $this->bimtekWithVerification->peserta()->attach($this->pesertaPending->id, [
            'status_verifikasi' => 'pending',
        ]);
        $this->bimtekWithVerification->peserta()->attach($this->pesertaRejected->id, [
            'status_verifikasi' => 'rejected',
        ]);

        // Bimtek WITHOUT verification
        $this->bimtekWithoutVerification = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'butuh_verifikasi_dokumen' => false,
        ]);

        $this->bimtekWithoutVerification->peserta()->attach($this->pesertaInvited->id);
    }

    #[Test]
    public function verified_peserta_can_access_absensi()
    {
        $response = $this->actingAs($this->pesertaVerified)
            ->get(route('bimtek.absensi.index', $this->bimtekWithVerification));

        $response->assertOk();
    }

    #[Test]
    public function verified_peserta_can_access_tugas()
    {
        $response = $this->actingAs($this->pesertaVerified)
            ->get(route('bimtek.tugas.index', $this->bimtekWithVerification));

        $response->assertOk();
    }

    #[Test]
    public function verified_peserta_can_access_sertifikat()
    {
        $response = $this->actingAs($this->pesertaVerified)
            ->get(route('bimtek.sertifikat.index', $this->bimtekWithVerification));

        $response->assertOk();
    }

    #[Test]
    public function invited_peserta_redirected_to_upload_form_from_absensi()
    {
        $response = $this->actingAs($this->pesertaInvited)
            ->get(route('bimtek.absensi.index', $this->bimtekWithVerification));

        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function pending_peserta_redirected_to_upload_form_from_tugas()
    {
        $response = $this->actingAs($this->pesertaPending)
            ->get(route('bimtek.tugas.index', $this->bimtekWithVerification));

        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function rejected_peserta_redirected_to_upload_form_from_sertifikat()
    {
        $response = $this->actingAs($this->pesertaRejected)
            ->get(route('bimtek.sertifikat.index', $this->bimtekWithVerification));

        $response->assertRedirect(route('bimtek.verifikasi-dokumen.upload-form', $this->bimtekWithVerification));
    }

    #[Test]
    public function pic_bypasses_middleware()
    {
        $response = $this->actingAs($this->pic)
            ->get(route('bimtek.absensi.index', $this->bimtekWithVerification));

        $response->assertOk();
    }

    #[Test]
    public function panitia_bypasses_middleware()
    {
        $response = $this->actingAs($this->panitia)
            ->get(route('bimtek.tugas.index', $this->bimtekWithVerification));

        $response->assertOk();
    }

    #[Test]
    public function middleware_skips_if_no_verification_required()
    {
        $response = $this->actingAs($this->pesertaInvited)
            ->get(route('bimtek.absensi.index', $this->bimtekWithoutVerification));

        // Should be allowed even though status might be 'invited'
        $response->assertOk();
    }

    #[Test]
    public function unverified_peserta_blocked_from_tugas_show()
    {
        $tugas = \App\Models\Tugas::factory()->create([
            'bimtek_id' => $this->bimtekWithVerification->id,
        ]);

        $response = $this->actingAs($this->pesertaInvited)
            ->get(route('bimtek.tugas.show', [$this->bimtekWithVerification, $tugas]));

        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function unverified_peserta_blocked_from_absensi_hadir()
    {
        $sesi = SesiAbsensi::factory()->create([
            'bimtek_id' => $this->bimtekWithVerification->id,
            'status' => 'terbuka',
        ]);

        $response = $this->actingAs($this->pesertaPending)
            ->post(route('bimtek.absensi.scan-qr', [$this->bimtekWithVerification, $sesi]), [
                'qr_code' => 'dummy-code',
            ]);

        $response->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function unverified_peserta_blocked_from_tugas_submit()
    {
        $tugas = \App\Models\Tugas::factory()->create([
            'bimtek_id' => $this->bimtekWithVerification->id,
        ]);

        $response = $this->actingAs($this->pesertaRejected)
            ->post(route('bimtek.tugas.submit', [$this->bimtekWithVerification, $tugas]), [
                'jawaban' => 'Test jawaban',
            ]);

        $response->assertRedirect(route('bimtek.verifikasi-dokumen.upload-form', $this->bimtekWithVerification));
    }
}
