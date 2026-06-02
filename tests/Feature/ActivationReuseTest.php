<?php

namespace Tests\Feature;

use App\Models\ActivationToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivationReuseTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_cannot_be_reused()
    {
        $user = User::factory()->create([
            'email' => 'reuse@test.com',
            'password' => bcrypt('oldpassword'),
        ]);

        [$token, $raw] = ActivationToken::generateFor($user, 7, null);

        // First activation should succeed
        $response1 = $this->post('/activate/'.$raw, [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response1->assertStatus(302);

        $token->refresh();
        $this->assertNotNull($token->used_at);
        $usedAt = $token->used_at->toDateTimeString();

        // Second activation attempt should be rejected
        $response2 = $this->post('/activate/'.$raw, [
            'password' => 'anotherpass123',
            'password_confirmation' => 'anotherpass123',
        ]);

        $response2->assertStatus(302);
        $response2->assertSessionHas('error');

        $token->refresh();
        $this->assertEquals($usedAt, $token->used_at->toDateTimeString());
    }
}
