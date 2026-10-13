<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
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
    public function test_password_can_be_updated(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(
            Hash::check('new-password', $user->refresh()->password)
        );
    }

    #[Test]
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = $this->createUser();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn(
                'updatePassword',
                'current_password'
            )
            ->assertRedirect('/profile');
    }
}