<?php

namespace App\Mail;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * @property User $user
 * @property Bimtek $bimtek
 * @property string $token
 */
class ActivationTokenMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;

    public Bimtek $bimtek;

    public string $token;

    public function __construct(User $user, Bimtek $bimtek, string $rawToken)
    {
        $this->user = $user;
        $this->bimtek = $bimtek;
        $this->token = $rawToken;
    }

    public function build(): Mailable
    {
        return $this->subject('Token Aktivasi Peserta Bimtek')
            ->view('emails.activation-token');
    }
}
