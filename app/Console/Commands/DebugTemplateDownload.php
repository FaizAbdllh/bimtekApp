<?php

namespace App\Console\Commands;

use App\Models\TemplateSertifikat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DebugTemplateDownload extends Command
{
    protected $signature = 'debug:template-download';
    protected $description = 'Debug template file paths and download functionality';

    public function handle()
    {
        $this->line('=== Debugging Template Download Issue ===');
        $this->line('');

        $templates = TemplateSertifikat::all();
        
        if ($templates->isEmpty()) {
            $this->error('No templates found in database');
            return;
        }

        foreach ($templates as $template) {
            $this->info("Template: {$template->nama_template} (ID: {$template->id})");
            $this->line("  File Path (DB): {$template->file_path}");
            
            $fullPath = "storage/app/public/{$template->file_path}";
            $this->line("  Full Path: {$fullPath}");
            
            // Check if file exists
            if (Storage::disk('public')->exists($template->file_path)) {
                $this->line('  ✓ File EXISTS in storage');
                $size = Storage::disk('public')->size($template->file_path);
                $this->line("  File Size: {$size} bytes");
            } else {
                $this->error('  ✗ File NOT found in storage');
            }
            
            // Check actual filesystem
            if (file_exists(storage_path("app/public/{$template->file_path}"))) {
                $this->line('  ✓ File EXISTS on filesystem');
            } else {
                $this->error('  ✗ File NOT found on filesystem');
            }
            
            $this->line('');
        }

        $this->line('=== Symlink Status ===');
        $publicStoragePath = public_path('storage');
        if (file_exists($publicStoragePath)) {
            if (is_link($publicStoragePath)) {
                $this->info('✓ Symlink public/storage EXISTS (symbolic link)');
                $this->line('  Target: ' . readlink($publicStoragePath));
            } else if (is_dir($publicStoragePath)) {
                $this->info('✓ Directory public/storage EXISTS (junction or directory)');
            } else {
                $this->warn('? public/storage EXISTS but type unknown');
            }
        } else {
            $this->error('✗ public/storage NOT found');
        }
        
        $this->line('');
        $this->line('=== Download URL Test ===');
        $template = $templates->first();
        if ($template) {
            $downloadUrl = route('admin.template-sertifikat.download', $template);
            $this->line("Download URL: {$downloadUrl}");
        }
    }
}
