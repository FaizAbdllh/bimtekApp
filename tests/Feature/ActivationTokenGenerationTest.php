<?php

namespace Tests\Feature;

use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActivationTokenGenerationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function panitia_can_generate_activation_token_for_peserta(): void
    {
        $role = Role::create(['nama_peran' => 'Pegawai Internal']);

        $pic = User::factory()->create(['role_id' => $role->id]);
        $panitia = User::factory()->create(['role_id' => $role->id]);

        $bimtek = Bimtek::factory()->create([
            'pic_user_id' => $pic->id,
        ]);

        // attach panitia to bimtek
        $bimtek->users()->attach($panitia->id, ['id' => (string) \Illuminate\Support\Str::uuid(), 'peran_kontekstual' => 'panitia']);

        // create a peserta user (not yet activated)
        $peserta = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($panitia)
            ->post(route('peserta.generate-token', [$bimtek, $peserta]));

        $response->assertRedirect();

        // Ensure activation token record exists for peserta
        $this->assertDatabaseHas('activation_tokens', [
            'user_id' => $peserta->id,
        ]);
    }
}
