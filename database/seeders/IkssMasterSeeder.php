<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IkssMaster;

class IkssMasterSeeder extends Seeder
{
    public function run(): void
    {
        IkssMaster::create([
            'kode_indikator' => '1.1.1.1',
            'sasaran_kegiatan' => 'Terwujudnya Penyediaan Data dan Insight Statistik Kependudukan dan Ketenagakerjaan yang Berkualitas',
            'indikator_kinerja' => 'Persentase Publikasi/Laporan Statistik Kependudukan dan Ketenagakerjaan yang Berkualitas',
            'target' => '100 Persen'
        ]);

        IkssMaster::create([
            'kode_indikator' => '2.1.4.1',
            'sasaran_kegiatan' => 'Terwujudnya Kapasitas Tata Kelola Pemerintahan Desa Untuk Menghasilkan Statistik Berkualitas',
            'indikator_kinerja' => 'Persentase Kumulatif Desa Yang Berpredikat Desa Cinta Statistik',
            'target' => '19,52 Persen'
        ]);

        IkssMaster::create([
            'kode_indikator' => '3.2.4.1',
            'sasaran_kegiatan' => 'Tersedianya Dukungan Manajemen pada BPS Provinsi dan BPS Kabupaten/Kota',
            'indikator_kinerja' => 'Nilai SAKIP oleh Inspektorat',
            'target' => '74,45 Poin'
        ]);
    }
}