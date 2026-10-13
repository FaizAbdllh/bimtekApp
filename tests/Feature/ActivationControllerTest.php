<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActivationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $rolePeserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rolePeserta = Role::create([
            'nama_peran' => 'Peserta Eksternal',
        ]);
    }

    private function createRegistrationBimtek(bool $requiresVerification = true): Bimtek
    {
        $bimtek = Bimtek::factory()->create([
            'status' => 'registrasi',
            'butuh_verifikasi_dokumen' => $requiresVerification,
        ]);

        $bimtek->forceFill([
            'invite_code' => 'INVITE-'.strtoupper(str()->random(8)),
        ])->save();

        return $bimtek;
    }

    #[Test]
    public function logged_in_user_can_join_bimtek_from_invite_link()
    {
        $bimtek = $this->createRegistrationBimtek();
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role_id' => $this->rolePeserta->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('register.join'), [
                'invite_code' => $bimtek->invite_code,
            ]);

        $response->assertRedirect(
            route('bimtek.verifikasi-dokumen.upload-form', $bimtek)
        );

        $this->assertDatabaseHas('bimtek_pesertas', [
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'status_verifikasi' => 'invited',
        ]);

        $this->assertNotNull(
            DB::table('bimtek_pesertas')
                ->where('bimtek_id', $bimtek->id)
                ->where('user_id', $user->id)
                ->value('notified_at')
        );
    }

    #[Test]
    public function logged_in_user_can_join_bimtek_without_document_verification()
    {
        $bimtek = $this->createRegistrationBimtek(false);
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role_id' => $this->rolePeserta->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('register.join'), [
                'invite_code' => $bimtek->invite_code,
            ]);

        $response->assertRedirect(route('bimtek.show', $bimtek));

        $this->assertDatabaseHas('bimtek_pesertas', [
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'status_verifikasi' => 'verified',
        ]);
    }

    #[Test]
    public function joining_existing_bimtek_does_not_duplicate_participant()
    {
        $bimtek = $this->createRegistrationBimtek();
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role_id' => $this->rolePeserta->id,
        ]);

        $bimtek->peserta()->attach($user->id, [
            'status_verifikasi' => 'invited',
            'notified_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('register.join'), [
                'invite_code' => $bimtek->invite_code,
            ]);

        $participantCount = DB::table('bimtek_pesertas')
            ->where('bimtek_id', $bimtek->id)
            ->where('user_id', $user->id)
            ->count();

        $this->assertSame(1, $participantCount);
    }

    #[Test]
    public function login_from_invite_link_redirects_user_to_upload_form()
    {
        $bimtek = $this->createRegistrationBimtek();

        $user = User::factory()->create([
            'role_id' => $this->rolePeserta->id,
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $loginPage = $this->get(route('login', [
            'invite_code' => $bimtek->invite_code,
        ]));

        $loginPage->assertOk();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(
            route('bimtek.verifikasi-dokumen.upload-form', $bimtek)
        );

        $this->assertDatabaseHas('bimtek_pesertas', [
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
            'status_verifikasi' => 'invited',
        ]);
    }

    #[Test]
    public function user_cannot_join_bimtek_after_registration_is_closed()
    {
        $bimtek = $this->createRegistrationBimtek();
        $bimtek->update([
            'status' => 'persiapan_selesai',
        ]);
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role_id' => $this->rolePeserta->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('register.join'), [
                'invite_code' => $bimtek->invite_code,
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas(
            'error',
            'Masa registrasi bimtek ini telah ditutup.'
        );

        $this->assertDatabaseMissing('bimtek_pesertas', [
            'bimtek_id' => $bimtek->id,
            'user_id' => $user->id,
        ]);
    }
    
    #[Test]
    public function existing_account_is_not_overwritten_during_registration()
    {
        $bimtek = $this->createRegistrationBimtek();

        $user = User::factory()->create([
            'role_id' => $this->rolePeserta->id,
            'email' => 'existing@example.com',
            'password' => Hash::make('original-password'),
        ]);

        $response = $this->post(route('register.submit'), [
            'invite_code' => $bimtek->invite_code,
            'name' => 'Nama Baru',
            'email' => $user->email,
            'nip' => '123456789012345678',
            'asal_instansi' => 'Instansi Lama',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(
            route('login', ['invite_code' => $bimtek->invite_code])
        );

        $user->refresh();

        $this->assertTrue(
            Hash::check('original-password', $user->password)
        );

        $this->assertFalse(
            Hash::check('new-password', $user->password)
        );
    }
}