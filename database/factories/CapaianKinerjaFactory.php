<?php

namespace Database\Factories;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CapaianKinerja>
 */
class CapaianKinerjaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ikss_master_id' => IkssMaster::factory(),
            'periode_id' => Periode::factory(),
            'target_pk' => null,
            'target_tw' => null,
            'realisasi' => null,
            'capaian_persen' => null,
            'capaian_tw_sebelumnya' => null,
            'link_bukti' => null,
            'kendala' => null,
            'status' => 'Kosong',
            'updated_by' => null,
        ];
    }
}
