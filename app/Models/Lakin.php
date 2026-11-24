<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lakin extends Model
{
    public const TYPES = ['Draft', 'Final'];

    protected $fillable = [
        'tahun',
        'tipe',
        'judul',
        'link_dokumen',
        'file_path',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
        ];
    }
}
