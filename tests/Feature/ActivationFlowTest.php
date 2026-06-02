<?php

namespace Tests\Feature;

use App\Models\ActivationToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ActivationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_activation_marks_token_used_and_sets_password()
    {
        $user = User::factory()->create([
            'email' => 'test-activation@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        [$token, $raw] = ActivationToken::generateFor($user, 7, null);

        $response = $this->post('/activate/'.$raw, [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(302);

        $token->refresh();
        $this->assertNotNull($token->used_at, 'Token used_at should be set');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}
