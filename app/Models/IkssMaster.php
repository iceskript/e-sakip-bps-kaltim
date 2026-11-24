<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IkssMaster extends Model
{
    use HasFactory;

    protected $table = 'ikss_masters';

    protected $fillable = [
        'kode_indikator',
        'sasaran_strategis',
        'sasaran_kegiatan',
        'indikator_kinerja',
        'target',
    ];

    public function capaianKinerjas(): HasMany
    {
        return $this->hasMany(CapaianKinerja::class);
    }
}
