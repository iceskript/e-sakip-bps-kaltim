<?php

namespace Tests\Feature;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
