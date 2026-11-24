<?php

namespace Tests\Feature;

use App\Models\CapaianKinerja;
use App\Models\Dokumen;
use App\Models\IkssMaster;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MonitoringIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_page_requires_authentication(): void
    {
        $this->get(route('monitoring.index'))
            ->assertRedirect(route('login'));
    }

    public function test_monitoring_page_filters_documents_by_year_and_quarter(): void
    {
        $this->actingAs(User::factory()->create());

        Storage::fake('public');
        Storage::disk('public')->put('monitoring/laporan.pdf', 'document');

        Dokumen::factory()->create([
            'menu_key' => 'monitoring_kinerja',
            'judul' => 'Laporan Monitoring Triwulan II',
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'kategori' => 'Template Narasi',
            'jenis' => 'File',
            'url_link' => null,
            'file_path' => 'monitoring/laporan.pdf',
            'filesize' => '4.5 MB',
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'monitoring_kinerja',
            'judul' => 'Tautan Monitoring Triwulan II',
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'url_link' => 'https://example.com/monitoring',
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'monitoring_kinerja',
            'judul' => 'Tautan tidak aman',
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'url_link' => 'javascript:alert(1)',
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'monitoring_kinerja',
            'judul' => 'Dokumen Monitoring Periode TW1',
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ]);
        Dokumen::factory()->create([
            'menu_key' => 'renstra',
            'judul' => 'Renstra bukan monitoring',
            'tahun' => 2026,
            'triwulan' => 'TW2',
        ]);

        $this->get(route('monitoring.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertOk()
            ->assertSee('Monitoring Capaian Kinerja')
            ->assertSee('Laporan Monitoring Triwulan II')
            ->assertSee('TW2 · Template Narasi')
            ->assertSee('File · 4.5 MB')
            ->assertSee(Storage::disk('public')->url('monitoring/laporan.pdf'), false)
            ->assertSee('Download')
            ->assertSee('Tautan Monitoring Triwulan II')
            ->assertSee('https://example.com/monitoring', false)
            ->assertSee('Buka')
            ->assertSee('Tautan tidak aman')
            ->assertDontSee('javascript:alert(1)', false)
            ->assertDontSee('Dokumen Monitoring Periode TW1')
            ->assertDontSee('Renstra bukan monitoring')
            ->assertDontSee('Tambah IKSS')
            ->assertDontSee('Edit IKSS')
            ->assertDontSee('Hapus IKSS')
            ->assertDontSee('Master Data IKSS');
    }

    public function test_monitoring_page_displays_an_empty_state_for_a_period_without_documents(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('monitoring.index', ['tahun' => 2025, 'triwulan' => 'TW4']))
            ->assertOk()
            ->assertSee('Belum ada data monitoring')
            ->assertSee('Semua TW')
            ->assertSee('TW IV');
    }

    public function test_monitoring_ikss_table_uses_the_capaian_for_the_selected_period(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create([
            'sasaran_kegiatan' => 'Meningkatnya kualitas statistik',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu',
        ]);
        $firstQuarter = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW1',
            'is_active' => false,
        ]);
        $secondQuarter = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $firstQuarter->id,
            'link_bukti' => 'https://example.test/bukti-tw1',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $secondQuarter->id,
            'link_bukti' => 'https://example.test/bukti-tw2',
        ]);

        $this->get(route('monitoring.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertOk()
            ->assertSee('Daftar IKSS')
            ->assertSee('Sasaran Kegiatan')
            ->assertSee('Meningkatnya kualitas statistik')
            ->assertSee('Persentase publikasi tepat waktu')
            ->assertSee('Link Dokumen Sumber')
            ->assertSee('Link Bukti Tindak Lanjut')
            ->assertSee('href="https://example.test/bukti-tw2"', false)
            ->assertDontSee('href="https://example.test/bukti-tw1"', false)
            ->assertDontSee('Edit IKSS')
            ->assertDontSee('Hapus IKSS');
    }

    public function test_monitoring_all_filters_choose_the_latest_capaian_period(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create();
        $olderPeriod = Periode::factory()->create([
            'tahun' => 2025,
            'triwulan' => 'TW4',
            'is_active' => false,
        ]);
        $newerPeriod = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $olderPeriod->id,
            'link_bukti' => 'https://example.test/bukti-2025',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $newerPeriod->id,
            'link_bukti' => 'https://example.test/bukti-2026',
        ]);

        $this->get(route('monitoring.index'))
            ->assertOk()
            ->assertSee('href="https://example.test/bukti-2026"', false)
            ->assertDontSee('href="https://example.test/bukti-2025"', false);
    }
}
