<?php

namespace Database\Factories;

use App\Models\IkssMaster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IkssMaster>
 */
class IkssMasterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_indikator' => fake()->unique()->numerify('1.1.1.##'),
            'sasaran_kegiatan' => fake()->sentence(),
            'indikator_kinerja' => fake()->sentence(),
            'target' => '100 Persen',
        ];
    }
}
