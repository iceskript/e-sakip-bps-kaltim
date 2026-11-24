<?php

namespace Tests\Feature;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use ZipArchive;

class NotulaTest extends TestCase
{
    use RefreshDatabase;

    public function test_notula_page_requires_authentication(): void
    {
        $this->get(route('notula.index'))
            ->assertRedirect(route('login'));
    }

    public function test_notula_page_shows_period_preview_and_status_counts(): void
    {
        $this->actingAs(User::factory()->create());
        $period = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW3',
            'is_active' => true,
        ]);
        $completeIndicator = IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.1',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu',
        ]);
        $partialIndicator = IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.2',
            'indikator_kinerja' => 'Persentase layanan statistik',
        ]);
        IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.3',
            'indikator_kinerja' => 'Indikator belum diisi',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $completeIndicator->id,
            'periode_id' => $period->id,
            'target_pk' => '100.00',
            'target_tw' => '25.00',
            'realisasi' => '20.00',
            'capaian_persen' => '80.00',
            'status' => 'Sudah Diisi',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $partialIndicator->id,
            'periode_id' => $period->id,
            'realisasi' => '10.00',
            'status' => 'Sebagian',
        ]);

        $this->get(route('notula.index', ['tahun' => 2026, 'triwulan' => 'TW3']))
            ->assertOk()
            ->assertSee('Generate Notula Monitoring Kinerja')
            ->assertSee('4. Cek Data Hasil Pembacaan')
            ->assertSee('5. Unduh Notula')
            ->assertSee('Persentase publikasi tepat waktu')
            ->assertSee('80%')
            ->assertSee('20%')
            ->assertViewHas('counts', [
                'read' => 3,
                'complete' => 1,
                'partial' => 1,
                'empty' => 1,
            ]);
    }

    public function test_meeting_information_is_saved_in_the_user_session(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('notula.storeRapat'), [
            'tahun' => 2026,
            'triwulan' => 'TW3',
            'satuan_kerja' => 'BPS Provinsi Kalimantan Timur',
            'nilai_sakip' => '82.5',
            'predikat' => 'A',
            'tanggal_rapat' => '2026-11-24',
            'waktu' => '09.00 WITA — selesai',
            'tempat' => 'Ruang Rapat',
            'pimpinan_rapat' => 'Kepala BPS',
            'nama_kepala_satker' => 'Nama Kepala',
            'nama_notulis' => 'Nama Notulis',
            'kota' => 'Samarinda',
        ])
            ->assertRedirect(route('notula.index', [
                'tahun' => 2026,
                'triwulan' => 'TW3',
                'source' => 'app',
            ]))
            ->assertSessionHas('success', 'Informasi rapat berhasil disimpan.')
            ->assertSessionHas('notula.rapat.tempat', 'Ruang Rapat');
    }

    public function test_invalid_meeting_date_does_not_replace_saved_information(): void
    {
        $this->actingAs(User::factory()->create());
        session(['notula.rapat.tempat' => 'Ruang sebelumnya']);

        $this->from(route('notula.index'))
            ->post(route('notula.storeRapat'), [
                'tahun' => 2026,
                'triwulan' => 'TW3',
                'satuan_kerja' => 'BPS Provinsi Kalimantan Timur',
                'tanggal_rapat' => 'not-a-date',
            ])
            ->assertRedirect(route('notula.index'))
            ->assertSessionHasErrors('tanggal_rapat')
            ->assertSessionHas('notula.rapat.tempat', 'Ruang sebelumnya');
    }

    public function test_valid_excel_upload_is_stored_on_the_private_disk(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('local');
        $upload = $this->makeExcelUpload();

        $this->post(route('notula.uploadExcel'), [
            'tahun' => 2026,
            'triwulan' => 'TW3',
            'source' => 'excel',
            'file' => $upload,
        ])
            ->assertRedirect(route('notula.index', [
                'tahun' => 2026,
                'triwulan' => 'TW3',
                'source' => 'excel',
            ]))
            ->assertSessionHas('success', 'File Excel berhasil diunggah dan disimpan secara privat.')
            ->assertSessionHas('notula.upload.name', 'kertas-kerja.xlsx');

        $path = session('notula.upload.path');
        $this->assertIsString($path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_non_excel_upload_is_rejected_without_storage_changes(): void
    {
        $this->actingAs(User::factory()->create());
        Storage::fake('local');

        $this->from(route('notula.index'))
            ->post(route('notula.uploadExcel'), [
                'tahun' => 2026,
                'triwulan' => 'TW3',
                'file' => UploadedFile::fake()->create('catatan.txt', 10, 'text/plain'),
            ])
            ->assertRedirect(route('notula.index'))
            ->assertSessionHasErrors('file');

        Storage::disk('local')->assertDirectoryEmpty('notula/imports');
    }

    public function test_notula_download_is_created_for_the_selected_period(): void
    {
        $this->actingAs(User::factory()->create());
        $period = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW3',
            'is_active' => false,
        ]);
        $ikssMaster = IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.1',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $period->id,
            'status' => 'Sudah Diisi',
        ]);

        $response = $this->post(route('notula.generate'), [
            'tahun' => 2026,
            'triwulan' => 'TW3',
            'format' => 'docx',
        ]);

        $response->assertDownload('notula-monitoring-2026-tw3.docx');
        $this->assertOfficeArchive($response, [
            '[Content_Types].xml',
            '_rels/.rels',
            'word/document.xml',
        ]);
    }

    public function test_kertas_kerja_download_is_created_for_the_selected_period(): void
    {
        $this->actingAs(User::factory()->create());
        $period = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW3',
            'is_active' => false,
        ]);
        $ikssMaster = IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.1',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $period->id,
            'status' => 'Sudah Diisi',
        ]);

        $response = $this->post(route('notula.generate'), [
            'tahun' => 2026,
            'triwulan' => 'TW3',
            'format' => 'xlsx',
        ]);

        $response->assertDownload('kertas-kerja-2026-tw3.xlsx');
        $this->assertOfficeArchive($response, [
            '[Content_Types].xml',
            'xl/workbook.xml',
            'xl/worksheets/sheet1.xml',
        ]);
    }

    /**
     * @param  array<int, string>  $expectedFiles
     */
    private function assertOfficeArchive(TestResponse $response, array $expectedFiles): void
    {
        $downloadPath = $response->baseResponse->getFile()->getPathname();
        $archive = new ZipArchive;
        $this->assertSame(true, $archive->open($downloadPath));

        try {
            foreach ($expectedFiles as $expectedFile) {
                $this->assertNotFalse($archive->locateName($expectedFile));
            }
        } finally {
            $archive->close();

            if (is_file($downloadPath)) {
                unlink($downloadPath);
            }
        }
    }

    private function makeExcelUpload(): UploadedFile
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'notula-test-');
        $this->assertIsString($temporaryPath);

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE));
        $archive->addFromString('[Content_Types].xml', '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>');
        $archive->addFromString('xl/workbook.xml', '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"/>');
        $this->assertTrue($archive->close());

        $contents = file_get_contents($temporaryPath);
        unlink($temporaryPath);
        $this->assertIsString($contents);

        return UploadedFile::fake()->createWithContent('kertas-kerja.xlsx', $contents);
    }
}
