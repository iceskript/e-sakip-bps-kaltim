<?php

namespace App\Models;

use Database\Factories\CapaianKinerjaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapaianKinerja extends Model
{
    /** @use HasFactory<CapaianKinerjaFactory> */
    use HasFactory;

    protected $table = 'capaian_kinerjas';

    protected $fillable = [
        'ikss_master_id',
        'periode_id',
        'target_pk',
        'target_tw',
        'realisasi',
        'capaian_persen',
        'capaian_tw_sebelumnya',
        'link_bukti',
        'kendala',
        'status',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'target_pk' => 'decimal:2',
            'target_tw' => 'decimal:2',
            'realisasi' => 'decimal:2',
            'capaian_persen' => 'decimal:2',
            'capaian_tw_sebelumnya' => 'decimal:2',
        ];
    }

    public function ikssMaster(): BelongsTo
    {
        return $this->belongsTo(IkssMaster::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
