<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create([
            'nama_peran' => 'Peserta Eksternal',
        ]);
    }

    private function createUser(): User
    {
        $role = Role::where('nama_peran', 'Peserta Eksternal')->firstOrFail();

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    #[Test]
    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->get('/confirm-password');

        $response->assertStatus(200);
    }

    #[Test]
    public function test_password_can_be_confirmed(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'password',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors();
    }
}