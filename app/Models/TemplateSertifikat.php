<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateSertifikat extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'template_sertifikats';

    protected $fillable = [
        'nama_template',
        'file_path',
    ];

    /**
     * Get all sertifikats using this template.
     * Note: This is for reference, actual generation may not store template_id
     */
}
