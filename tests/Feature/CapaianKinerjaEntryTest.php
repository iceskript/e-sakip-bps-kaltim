<?php

namespace Tests\Feature;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CapaianKinerjaEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_entry_page_filters_period_and_shows_status_and_last_editor(): void
    {
        $operator = User::factory()->create(['name' => 'Operator Uji']);
        $this->actingAs($operator);
        $ikssMaster = IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.1',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu',
            'sasaran_kegiatan' => 'Data statistik berkualitas',
        ]);
        $withoutCapaian = IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.2',
            'indikator_kinerja' => 'Indikator belum diisi',
        ]);
        $periode = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $periode->id,
            'realisasi' => '20.00',
            'status' => 'Sebagian',
            'updated_by' => $operator->id,
            'updated_at' => '2026-09-10 06:28:00',
        ]);
        $otherPeriod = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW1',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $otherPeriod->id,
            'realisasi' => '99.00',
            'status' => 'Sudah Diisi',
        ]);

        $this->get(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertOk()
            ->assertSee('Entry Capaian Kinerja Triwulanan')
            ->assertSee('1.1.1.1')
            ->assertSee('Persentase publikasi tepat waktu')
            ->assertSee('Sebagian')
            ->assertDontSee('Belum Diisi')
            ->assertSee('T1 — KEBIJAKAN BERBASIS DATA')
            ->assertSee('aria-valuetext="0 dari 2 IKU telah diisi"', false)
            ->assertSee('Tambah Indikator')
            ->assertSee('action="'.route('ikss.store').'"', false)
            ->assertSee('data-edit-url="'.e(route('ikss.capaian.edit', [
                'ikssMaster' => $ikssMaster,
                'tahun' => 2026,
                'triwulan' => 'TW2',
            ])).'"', false)
            ->assertSee('Tindak Lanjut TW Sebelumnya')
            ->assertSee('Simpan Data')
            ->assertSee('Hapus dikunci: ada riwayat capaian')
            ->assertSee('aria-label="Hapus indikator 1.1.1.2"', false)
            ->assertDontSee('aria-label="Hapus indikator 1.1.1.1"', false)
            ->assertDontSee('Tambah IKSS')
            ->assertSee('Operator Uji')
            ->assertSee('2026-09-10 06:28')
            ->assertDontSee('value="99.00"', false);
    }

    public function test_entry_page_groups_indicators_by_strategic_objective_and_shows_completion_progress(): void
    {
        $operator = User::factory()->create();
        $this->actingAs($operator);
        $t1Indicator = IkssMaster::factory()->create(['kode_indikator' => '1.1.1.1']);
        IkssMaster::factory()->create([
            'kode_indikator' => '9.1.1.1',
            'sasaran_strategis' => 'T2',
        ]);
        IkssMaster::factory()->create(['kode_indikator' => '3.1.1.1']);
        $periode = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW1',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $t1Indicator->id,
            'periode_id' => $periode->id,
            'status' => 'Sudah Diisi',
            'updated_by' => $operator->id,
        ]);

        $this->get(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW1']))
            ->assertOk()
            ->assertSee('T1 — KEBIJAKAN BERBASIS DATA')
            ->assertSee('T2 — SISTEM STATISTIK NASIONAL')
            ->assertSee('T3 — TATA KELOLA BPS')
            ->assertSee('TW I — 2026')
            ->assertSee('aria-valuetext="1 dari 3 IKU telah diisi"', false);
    }

    public function test_operator_can_add_an_indicator_from_the_entry_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('ikss.store'), [
            '_dialog' => 'entry-ikss-create',
            'return_to' => 'entry',
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'kode_indikator' => '7.1.1.1',
            'sasaran_strategis' => 'T2',
            'sasaran_kegiatan' => 'Meningkatnya kualitas data statistik.',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu.',
            'target' => '100 Persen',
        ])
            ->assertRedirect(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertSessionHas('success', 'Indikator IKSS berhasil ditambahkan.');

        $this->assertDatabaseHas('ikss_masters', [
            'kode_indikator' => '7.1.1.1',
            'sasaran_strategis' => 'T2',
            'sasaran_kegiatan' => 'Meningkatnya kualitas data statistik.',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu.',
            'target' => '100 Persen',
        ]);
    }

    public function test_operator_can_delete_an_indicator_from_the_entry_page(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create();

        $this->delete(route('ikss.destroy', $ikssMaster), [
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'return_to' => 'entry',
        ])
            ->assertRedirect(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertSessionHas('success', 'Indikator IKSS berhasil dihapus.');

        $this->assertDatabaseMissing('ikss_masters', ['id' => $ikssMaster->id]);
    }

    public function test_indicator_with_capaian_history_cannot_be_deleted_from_the_entry_page(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create();
        $periode = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW2',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $periode->id,
        ]);

        $this->delete(route('ikss.destroy', $ikssMaster), [
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'return_to' => 'entry',
        ])
            ->assertRedirect(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertSessionHasErrors('ikss');

        $this->assertModelExists($ikssMaster);
    }

    public function test_invalid_indicator_from_entry_reopens_the_add_dialog(): void
    {
        $this->actingAs(User::factory()->create());
        IkssMaster::factory()->create(['kode_indikator' => '7.1.1.1']);
        $entryUrl = route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']);

        $this->from($entryUrl)->followingRedirects()->post(route('ikss.store'), [
            '_dialog' => 'entry-ikss-create',
            'return_to' => 'entry',
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'kode_indikator' => '7.1.1.1',
            'sasaran_kegiatan' => 'Sasaran kegiatan.',
            'indikator_kinerja' => 'Indikator kinerja.',
            'target' => '100 Persen',
        ])
            ->assertOk()
            ->assertSee('x-init="$nextTick(() => $refs.createEntryIkssDialog.showModal())"', false)
            ->assertSeeText('The kode indikator has already been taken.');

        $this->assertDatabaseCount('ikss_masters', 1);
    }

    public function test_indicator_cannot_be_assigned_to_an_unknown_strategic_objective(): void
    {
        $this->actingAs(User::factory()->create());

        $this->from(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->post(route('ikss.store'), [
                '_dialog' => 'entry-ikss-create',
                'return_to' => 'entry',
                'tahun' => 2026,
                'triwulan' => 'TW2',
                'kode_indikator' => '7.1.1.1',
                'sasaran_strategis' => 'T4',
                'sasaran_kegiatan' => 'Sasaran kegiatan.',
                'indikator_kinerja' => 'Indikator kinerja.',
                'target' => '100 Persen',
            ])
            ->assertRedirect(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertSessionHasErrors('sasaran_strategis');

        $this->assertDatabaseCount('ikss_masters', 0);
    }

    public function test_operator_can_save_entry_without_overwriting_existing_targets(): void
    {
        $operator = User::factory()->create();
        $this->actingAs($operator);
        $ikssMaster = IkssMaster::factory()->create();
        $periode = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $periode->id,
            'target_pk' => '100.00',
            'target_tw' => '25.00',
            'realisasi' => '20.00',
            'status' => 'Sebagian',
        ]);

        $this->put(route('capaian-kinerja.update', $ikssMaster), [
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'realisasi' => '83.50',
            'link_bukti' => 'https://example.test/bukti',
            'kendala' => 'Menunggu validasi data.',
        ])->assertRedirect(route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']));

        $capaian = CapaianKinerja::query()
            ->where('ikss_master_id', $ikssMaster->id)
            ->where('periode_id', $periode->id)
            ->firstOrFail();

        $this->assertSame('83.50', $capaian->realisasi);
        $this->assertSame('100.00', $capaian->target_pk);
        $this->assertSame('25.00', $capaian->target_tw);
        $this->assertSame('https://example.test/bukti', $capaian->link_bukti);
        $this->assertSame('Menunggu validasi data.', $capaian->kendala);
        $this->assertSame('Sudah Diisi', $capaian->status);
        $this->assertSame($operator->id, $capaian->updated_by);
    }

    public function test_modal_json_endpoint_loads_indicator_and_previous_quarter_history(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create([
            'kode_indikator' => '1.1.1.1',
            'indikator_kinerja' => 'Publikasi berkualitas',
        ]);
        $previousPeriod = Periode::factory()->create([
            'tahun' => 2025,
            'triwulan' => 'TW4',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $previousPeriod->id,
            'analisis_capaian' => [
                'kendala' => 'Kendala triwulan lalu',
                'solusi' => 'Solusi triwulan lalu',
                'rencana_tindak_lanjut' => 'Rencana sebelumnya',
                'pic' => 'Petugas A',
                'batas_waktu' => '2025-12-30',
            ],
            'tindak_lanjut' => ['progres_pelaksanaan' => 75],
        ]);

        $this->withHeaders(['Accept' => 'application/json'])
            ->get(route('ikss.capaian.edit', [
                'ikssMaster' => $ikssMaster,
                'tahun' => 2026,
                'triwulan' => 'TW1',
            ]))
            ->assertOk()
            ->assertJsonPath('data.ikss.kode_indikator', '1.1.1.1')
            ->assertJsonPath('data.previous.label', 'TW IV')
            ->assertJsonPath('data.previous.kendala', 'Kendala triwulan lalu')
            ->assertJsonPath('data.previous.progres', 75);
    }

    public function test_modal_json_endpoint_seeds_legacy_capaian_values_into_the_selected_quarter(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create();
        $periode = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW1',
            'is_active' => false,
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $periode->id,
            'target_pk' => '100.00',
            'realisasi' => '25.00',
            'capaian_iku' => null,
        ]);

        $this->withHeaders(['Accept' => 'application/json'])
            ->get(route('ikss.capaian.edit', [
                'ikssMaster' => $ikssMaster,
                'tahun' => 2026,
                'triwulan' => 'TW1',
            ]))
            ->assertOk()
            ->assertJsonPath('data.capaian.capaian_iku.tw1.x', 25)
            ->assertJsonPath('data.capaian.capaian_iku.tw1.y', 100);
    }

    public function test_operator_can_save_all_modal_sections_as_json(): void
    {
        $operator = User::factory()->create();
        $this->actingAs($operator);
        $ikssMaster = IkssMaster::factory()->create();
        $payload = $this->validModalPayload();
        $payload['capaian_iku']['tw1'] = ['x' => 10, 'y' => 20];

        $this->withHeaders(['Accept' => 'application/json'])
            ->put(route('ikss.capaian.update', $ikssMaster), [
                'payload' => json_encode($payload),
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Data capaian berhasil disimpan.');

        $capaian = CapaianKinerja::query()
            ->where('ikss_master_id', $ikssMaster->id)
            ->firstOrFail();

        $this->assertSame('10.00', $capaian->realisasi);
        $this->assertSame('50.00', $capaian->capaian_persen);
        $this->assertSame('Sudah Diisi', $capaian->status);
        $this->assertSame('Perlu validasi data.', $capaian->analisis_capaian['kendala']);
        $this->assertSame('2905 BMA 004', $capaian->capaian_ro[0]['kode']);
        $this->assertSame(20, $capaian->tindak_lanjut['progres_pelaksanaan']);
        $this->assertSame($operator->id, $capaian->updated_by);
    }

    public function test_modal_json_endpoint_rejects_invalid_progress_without_saving(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create();
        $payload = $this->validModalPayload();
        $payload['tindak_lanjut']['progres_pelaksanaan'] = 101;

        $this->withHeaders(['Accept' => 'application/json'])
            ->put(route('ikss.capaian.update', $ikssMaster), [
                'payload' => json_encode($payload),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('tindak_lanjut.progres_pelaksanaan');

        $this->assertDatabaseCount('capaian_kinerjas', 0);
    }

    public function test_modal_uploads_private_evidence_and_serves_it_only_from_its_capaian_record(): void
    {
        Storage::fake('local');
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create();
        $payload = $this->validModalPayload();
        $photo = UploadedFile::fake()->createWithContent(
            'solution.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jN4kAAAAASUVORK5CYII='),
        );

        $this->withHeaders(['Accept' => 'application/json'])
            ->post(route('ikss.capaian.update', $ikssMaster), [
                '_method' => 'PUT',
                'payload' => json_encode($payload),
                'files' => [
                    'analisis_capaian' => [
                        'solution_points' => [
                            ['photos' => [$photo]],
                        ],
                    ],
                ],
            ])
            ->assertOk();

        $capaian = CapaianKinerja::query()
            ->where('ikss_master_id', $ikssMaster->id)
            ->firstOrFail();
        $storedPhoto = $capaian->analisis_capaian['solution_points'][0]['photos'][0];

        Storage::disk('local')->assertExists($storedPhoto['path']);

        $this->get(route('capaian-kinerja.files.download', [$capaian, $storedPhoto['id']]))
            ->assertOk();

        $otherIkss = IkssMaster::factory()->create();
        $otherCapaian = CapaianKinerja::factory()->create([
            'ikss_master_id' => $otherIkss->id,
            'periode_id' => $capaian->periode_id,
        ]);

        $this->get(route('capaian-kinerja.files.download', [$otherCapaian, $storedPhoto['id']]))
            ->assertNotFound();
    }

    public function test_invalid_entry_does_not_save_and_reopens_the_edit_dialog(): void
    {
        $this->actingAs(User::factory()->create());
        $ikssMaster = IkssMaster::factory()->create();
        $indexUrl = route('capaian-kinerja.index', ['tahun' => 2026, 'triwulan' => 'TW2']);

        $this->from($indexUrl)->followingRedirects()->put(route('capaian-kinerja.update', $ikssMaster), [
            '_dialog' => (string) $ikssMaster->id,
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'realisasi' => '5.00',
            'link_bukti' => 'invalid-url',
            'kendala' => '',
        ])
            ->assertOk()
            ->assertSee('name="link_bukti"', false)
            ->assertSee('x-init="$nextTick(() => $refs.entryDialog.showModal())"', false)
            ->assertSeeText('The link bukti field must be a valid URL.');

        $this->assertDatabaseCount('capaian_kinerjas', 0);
    }

    public function test_guest_is_redirected_from_entry_page(): void
    {
        auth()->logout();

        $this->get('/entry-capaian')
            ->assertRedirect(route('login'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validModalPayload(): array
    {
        $quarters = [
            'tw1' => ['x' => 0, 'y' => 0],
            'tw2' => ['x' => 0, 'y' => 0],
            'tw3' => ['x' => 0, 'y' => 0],
            'tw4' => ['x' => 0, 'y' => 0],
        ];

        return [
            'tahun' => 2026,
            'triwulan' => 'TW1',
            'capaian_iku' => $quarters,
            'analisis_capaian' => [
                'kendala' => 'Perlu validasi data.',
                'solusi' => 'Koordinasi dengan tim.',
                'solution_points' => [['uraian' => 'Koordinasi', 'photos' => []]],
                'rencana_tindak_lanjut' => 'Validasi lanjutan.',
                'pic' => 'Petugas A',
                'batas_waktu' => '2026-03-30',
                'link_bukti_kinerja' => null,
                'link_bukti_tl_sebelumnya' => null,
            ],
            'capaian_ro' => [
                [
                    'kode' => '2905 BMA 004',
                    'nama' => 'Publikasi/Laporan Survei Angkatan Kerja Nasional',
                    'vol' => 0,
                    'progres' => 0,
                    'narasi' => '',
                    'fotos' => [],
                    'laporan_pdf' => null,
                    'kegiatan' => [],
                ],
                [
                    'kode' => '2905 BMA 005',
                    'nama' => 'Publikasi/Laporan Statistik Kependudukan dan Ketenagakerjaan',
                    'vol' => 0,
                    'progres' => 0,
                    'narasi' => '',
                    'fotos' => [],
                    'laporan_pdf' => null,
                    'kegiatan' => [],
                ],
            ],
            'tindak_lanjut' => ['progres_pelaksanaan' => 20],
        ];
    }
}
