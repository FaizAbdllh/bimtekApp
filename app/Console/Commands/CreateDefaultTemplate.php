<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDefaultTemplate extends Command
{
    protected $signature = 'template:create-default';

    protected $description = 'Create a default certificate template file';

    public function handle()
    {
        $this->warn('⚠ Default template creation is disabled.');
        $this->line('');
        $this->info('📋 To use certificate templates:');
        $this->line('1. Create a certificate template in Microsoft Word');
        $this->line('2. Use these placeholders in your Word document:');
        $this->line('   ${NAMA_PESERTA}, ${NIP}, ${INSTANSI}');
        $this->line('   ${NOMOR_SERTIFIKAT}, ${JUDUL_BIMTEK}');
        $this->line('   ${TANGGAL_MULAI}, ${TANGGAL_SELESAI}');
        $this->line('   ${LOKASI}, ${TANGGAL_TERBIT}');
        $this->line('3. Save as .docx format');
        $this->line('4. Upload via: /admin/template-sertifikat/create');
        $this->line('');
        $this->info('✓ Real Word template required for proper formatting');

        return 0;
    }
}
