<?php

use Illuminate\Support\Facades\Mail;

// Test send email
Mail::raw('Test email dari SI Bimtek BBPMP Sumbar. Jika Anda menerima email ini, konfigurasi SMTP berhasil!', function ($message) {
    $message->to('faizabdullah2708@gmail.com')
            ->subject('Test Email - SI Bimtek BBPMP Sumbar');
});

echo "Email sent successfully!\n";
