<?php

namespace Tests\Feature;

use App\Models\ActivationToken;
use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivationTokenExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_token_cannot_be_used_to_activate()
    {
        // Create user and bimtek
        $user = User::factory()->create();
        $bimtek = Bimtek::factory()->create();

        // Generate token and manually set expires_at to past
        [$tokenModel, $raw] = ActivationToken::generateFor($user, 7, null);
        $tokenModel->bimtek_id = $bimtek->id;
        $tokenModel->expires_at = now()->subDays(1);
        $tokenModel->save();

        // Model-level assertions: token is expired and should not be considered usable
        $fresh = ActivationToken::find($tokenModel->id);
        $this->assertNull($fresh->used_at);
        $this->assertTrue($fresh->isExpired());
    }

    public function test_token_becomes_invalid_after_expiry_even_if_raw_known()
    {
        $user = User::factory()->create();
        $bimtek = Bimtek::factory()->create();

        [$tokenModel, $raw] = ActivationToken::generateFor($user, 1, null);
        $tokenModel->bimtek_id = $bimtek->id;
        $tokenModel->expires_at = now()->subMinute();
        $tokenModel->save();

        // Ensure ActivationToken::findByRawToken returns the model but isExpired() true
        $found = ActivationToken::findByRawToken($raw);
        $this->assertNotNull($found);
        $this->assertTrue($found->isExpired());
    }

    public function test_token_single_use_enforced()
    {
        $user = User::factory()->create();
        [$tokenModel, $raw] = ActivationToken::generateFor($user, 7, null);
        $this->assertNull($tokenModel->used_at);

        // Mark used
        $tokenModel->markUsed();
        $this->assertNotNull($tokenModel->used_at);

        // Subsequent attempts should find token but used_at is set
        $found = ActivationToken::findByRawToken($raw);
        $this->assertNotNull($found);
        $this->assertNotNull($found->used_at);
    }
}
