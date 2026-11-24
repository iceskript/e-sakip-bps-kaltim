<?php

namespace App\Models;

use Database\Factories\PeriodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    /** @use HasFactory<PeriodeFactory> */
    use HasFactory;

    public const QUARTERS = ['TW1', 'TW2', 'TW3', 'TW4'];

    protected $table = 'periodes';

    protected $fillable = [
        'tahun',
        'triwulan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function capaianKinerjas(): HasMany
    {
        return $this->hasMany(CapaianKinerja::class);
    }
}
