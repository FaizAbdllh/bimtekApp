<?php

namespace App\Mail;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PesertaCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $password,
        public ?Bimtek $bimtek = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Kredensial Akun SI Bimtek BBPMP Sumbar';
        
        if ($this->bimtek) {
            $subject = 'Undangan Peserta Bimtek: ' . $this->bimtek->judul_final;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.peserta-credentials',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!$this->bimtek) {
            return [];
        }

        $path = $this->bimtek->file_surat_undangan_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            return [];
        }

        $safeJudul = Str::slug($this->bimtek->judul_final ?: 'bimtek');

        return [
            Attachment::fromStorageDisk('public', $path)
                ->as("surat-undangan-{$safeJudul}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
