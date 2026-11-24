<?php

namespace App\Models;

use Database\Factories\DokumenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    /** @use HasFactory<DokumenFactory> */
    use HasFactory;

    protected $table = 'dokumens';

    protected $fillable = [
        'menu_key',
        'judul',
        'tahun',
        'triwulan',
        'kategori',
        'jenis',
        'url_link',
        'file_path',
        'filesize',
    ];
}
