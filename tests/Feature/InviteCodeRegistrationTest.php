<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InviteCodeRegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_is_blocked_when_bimtek_requires_invite_code_and_none_provided(): void
    {
        $role = Role::create(['nama_peran' => 'Pegawai Internal']);

        $bimtek = Bimtek::factory()->create([
            'pic_user_id' => User::factory()->create(['role_id' => $role->id])->id,
            'invite_code' => 'SECRET-CODE-123',
        ]);

        $post = [
            'name' => 'Peserta Test',
            'email' => 'peserta@example.test',
            'nip' => '19700101 0001 1',
        ];

        $response = $this->post(route('bimtek.daftar.register', $bimtek), $post);

        $response->assertRedirect();

        // Ensure user was not created because invite_code was required
        $this->assertDatabaseMissing('users', [
            'email' => 'peserta@example.test',
        ]);
    }

    #[Test]
    public function registration_succeeds_when_invite_code_is_provided(): void
    {
        $role = Role::create(['nama_peran' => 'Pegawai Internal']);

        $pic = User::factory()->create(['role_id' => $role->id]);

        $bimtek = Bimtek::factory()->create([
            'pic_user_id' => $pic->id,
            'invite_code' => 'SHARED-INVITE-456',
        ]);

        $post = [
            'name' => 'Peserta Test',
            'email' => 'peserta2@example.test',
            'nip' => '19700101 0002 1',
            'invite_code' => 'SHARED-INVITE-456',
        ];

        $response = $this->post(route('bimtek.daftar.register', $bimtek), $post);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'peserta2@example.test',
        ]);
    }
}
