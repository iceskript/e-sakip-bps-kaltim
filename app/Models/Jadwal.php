<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'judul',
        'kategori',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'selesai' => 'boolean',
        ];
    }
}
