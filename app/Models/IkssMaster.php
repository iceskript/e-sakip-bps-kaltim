<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkssMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_indikator', 
        'sasaran_kegiatan', 
        'indikator_kinerja', 
        'target'
    ];
}