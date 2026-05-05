<?php

namespace Tests\Feature;

use App\Mail\PesertaBimtekInvitedMail;
use App\Mail\PesertaCredentialsMail;
use App\Models\Bimtek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PesertaControllerVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;
    protected User $panitia;
    protected User $existingUser;
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
        $this->existingUser = User::factory()->create(['role_id' => $this->rolePeserta->id]);

        // Bimtek WITH verification
        $this->bimtekWithVerification = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'butuh_verifikasi_dokumen' => true,
            'jenis_dokumen_wajib' => ['surat_tugas', 'sppd'],
        ]);
        
        // Assign panitia to bimtek
        $this->bimtekWithVerification->users()->attach($this->panitia->id, [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'peran_kontekstual' => 'panitia',
        ]);

        // Bimtek WITHOUT verification
        $this->bimtekWithoutVerification = Bimtek::factory()->create([
            'pic_user_id' => $this->pic->id,
            'butuh_verifikasi_dokumen' => false,
            'jenis_dokumen_wajib' => null,
        ]);
        
        // Assign panitia to bimtek
        $this->bimtekWithoutVerification->users()->attach($this->panitia->id, [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'peran_kontekstual' => 'panitia',
        ]);
    }

    #[Test]
    public function assign_existing_peserta_sends_email_if_verification_required()
    {
        Mail::fake();

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.peserta.store', $this->bimtekWithVerification), [
                'user_ids' => [$this->existingUser->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check email sent
        Mail::assertSent(PesertaBimtekInvitedMail::class, function ($mail) {
            return $mail->hasTo($this->existingUser->email)
                && $mail->bimtek->id === $this->bimtekWithVerification->id;
        });
    }

    #[Test]
    public function assign_existing_peserta_sets_invited_status_if_verification_required()
    {
        Mail::fake();

        $this->actingAs($this->panitia)
            ->post(route('bimtek.peserta.store', $this->bimtekWithVerification), [
                'user_ids' => [$this->existingUser->id],
            ]);

        $this->assertDatabaseHas('bimtek_user', [
            'bimtek_id' => $this->bimtekWithVerification->id,
            'user_id' => $this->existingUser->id,
            'peran_kontekstual' => 'peserta',
            'status_verifikasi' => 'invited',
        ]);

        // Check notified_at is set by querying database directly
        $pivotRecord = \Illuminate\Support\Facades\DB::table('bimtek_user')
            ->where('bimtek_id', $this->bimtekWithVerification->id)
            ->where('user_id', $this->existingUser->id)
            ->first();

        $this->assertNotNull($pivotRecord->notified_at);
    }

    #[Test]
    public function assign_existing_peserta_does_not_send_email_if_no_verification()
    {
        Mail::fake();

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.peserta.store', $this->bimtekWithoutVerification), [
                'user_ids' => [$this->existingUser->id],
            ]);

        $response->assertRedirect();

        // No verification email should be sent
        Mail::assertNotSent(PesertaBimtekInvitedMail::class);

        // Status should be null (no verification needed)
        $this->assertDatabaseHas('bimtek_user', [
            'bimtek_id' => $this->bimtekWithoutVerification->id,
            'user_id' => $this->existingUser->id,
            'peran_kontekstual' => 'peserta',
            'status_verifikasi' => null,
        ]);
    }

    #[Test]
    public function store_new_peserta_sends_two_emails_if_verification_required()
    {
        Mail::fake();

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.peserta.store-new', $this->bimtekWithVerification), [
                'name' => 'New Peserta',
                'email' => 'newpeserta@example.com',
                'nip' => '123456789',
                'asal_instansi' => 'Test Instansi',
            ]);

        $response->assertRedirect();

        // Check both emails sent
        Mail::assertSent(PesertaCredentialsMail::class);
        Mail::assertSent(PesertaBimtekInvitedMail::class);
    }

    #[Test]
    public function store_new_peserta_sets_invited_status_correctly()
    {
        Mail::fake();

        $this->actingAs($this->panitia)
            ->post(route('bimtek.peserta.store-new', $this->bimtekWithVerification), [
                'name' => 'New Peserta',
                'email' => 'newpeserta@example.com',
            ]);

        $newUser = User::where('email', 'newpeserta@example.com')->first();

        $this->assertDatabaseHas('bimtek_user', [
            'bimtek_id' => $this->bimtekWithVerification->id,
            'user_id' => $newUser->id,
            'peran_kontekstual' => 'peserta',
            'status_verifikasi' => 'invited',
        ]);
    }

    #[Test]
    public function store_new_peserta_without_verification_only_sends_credentials_email()
    {
        Mail::fake();

        $this->actingAs($this->panitia)
            ->post(route('bimtek.peserta.store-new', $this->bimtekWithoutVerification), [
                'name' => 'New Peserta',
                'email' => 'newpeserta2@example.com',
            ]);

        // Only credentials email should be sent
        Mail::assertSent(PesertaCredentialsMail::class);
        Mail::assertNotSent(PesertaBimtekInvitedMail::class);
    }

    #[Test]
    public function success_message_mentions_verification_email_when_sent()
    {
        Mail::fake();

        $response = $this->actingAs($this->panitia)
            ->post(route('bimtek.peserta.store', $this->bimtekWithVerification), [
                'user_ids' => [$this->existingUser->id],
            ]);

        $response->assertSessionHas('success', function ($message) {
            return str_contains($message, 'Email undangan verifikasi dokumen telah dikirim');
        });
    }
}
