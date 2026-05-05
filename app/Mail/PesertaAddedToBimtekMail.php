<?php

namespace App\Mail;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PesertaAddedToBimtekMail extends Mailable
{
    use Queueable, SerializesModels;

    public Bimtek $bimtek;
    public User $peserta;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Bimtek $bimtek, User $peserta, string $loginUrl)
    {
        $this->bimtek = $bimtek;
        $this->peserta = $peserta;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Anda Ditambahkan ke Bimtek: ' . $this->bimtek->judul_final,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.peserta-added',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
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
