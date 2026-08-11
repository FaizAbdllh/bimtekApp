<?php

namespace App\Mail;

use App\Models\Bimtek;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DokumenVerifiedRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Bimtek $bimtek;
    public User $peserta;
    public string $status; // 'verified' or 'rejected'
    public ?string $catatan;
    public ?string $uploadUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Bimtek $bimtek, User $peserta, string $status, ?string $catatan = null, ?string $uploadUrl = null)
    {
        $this->bimtek = $bimtek;
        $this->peserta = $peserta;
        $this->status = $status;
        $this->catatan = $catatan;
        $this->uploadUrl = $uploadUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // 💡 PERBAIKAN: Menggunakan judul_rencana sebagai cadangan jika judul_final kosong
        $judul = $this->bimtek->judul_final ?? $this->bimtek->judul_rencana;
        
        $subject = $this->status === 'verified'
            ? 'Dokumen Disetujui: '.$judul
            : 'Dokumen Ditolak: '.$judul;

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
            // 💡 PERBAIKAN: Mengubah 'view' menjadi 'markdown' agar bisa membaca template UI Email Laravel
            view: 'emails.dokumen-verified-rejected',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}