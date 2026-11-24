<?php

namespace Database\Factories;

use App\Models\Dokumen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dokumen>
 */
class DokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_key' => 'renstra',
            'judul' => fake()->sentence(4),
            'tahun' => fake()->numberBetween(2020, 2030),
            'triwulan' => null,
            'kategori' => null,
            'jenis' => 'Link',
            'url_link' => 'https://example.com/dokumen',
            'file_path' => null,
            'filesize' => null,
        ];
    }
}
