<?php

namespace App\Mail;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivationTokenMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $bimtek;
    public $token;

    public function __construct(User $user, Bimtek $bimtek, string $rawToken)
    {
        $this->user = $user;
        $this->bimtek = $bimtek;
        $this->token = $rawToken;
    }

    public function build()
    {
        return $this->subject('Token Aktivasi Peserta Bimtek')
            ->view('emails.activation-token');
    }
}
