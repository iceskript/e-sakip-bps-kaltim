<?php

namespace Tests\Feature;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IkssMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_monitoring_displays_ikss_capaian_and_external_app_links(): void
    {
        $ikssMaster = IkssMaster::factory()->create([
            'kode_indikator' => '1.4.1.2',
            'sasaran_kegiatan' => 'Data statistik berkualitas',
            'indikator_kinerja' => 'Persentase publikasi tepat waktu',
            'target' => '100 Persen',
        ]);
        $periode = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW2',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $periode->id,
            'target_pk' => '100.00',
            'target_tw' => '25.00',
            'realisasi' => '20.00',
            'capaian_persen' => '80.00',
            'capaian_tw_sebelumnya' => '75.00',
            'status' => 'Sudah Diisi',
            'updated_by' => $this->user->id,
        ]);
        $previousQuarter = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $previousQuarter->id,
            'capaian_persen' => '99.99',
            'status' => 'Sudah Diisi',
        ]);

        $this->get(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW2']))
            ->assertOk()
            ->assertSee('Master Data IKSS')
            ->assertSee('1.4.1.2')
            ->assertSee('Tabel IKSS')
            ->assertSee('Link Dokumen Sumber')
            ->assertSee('Link Bukti Tindak Lanjut TW Sebelumnya')
            ->assertSee('Belum tersedia')
            ->assertSee('Data statistik berkualitas')
            ->assertSee('Persentase publikasi tepat waktu')
            ->assertSee('80.00')
            ->assertDontSee('value="99.99"', false)
            ->assertSee('Sudah Diisi')
            ->assertSee('Hapus dikunci: ada riwayat capaian')
            ->assertSee('https://esr.menpan.go.id/', false)
            ->assertSee('https://sinergi.web.bps.go.id/', false);
    }

    public function test_monitoring_paginates_ikss_using_the_selected_page_size(): void
    {
        foreach (range(1, 12) as $number) {
            IkssMaster::factory()->create([
                'kode_indikator' => sprintf('IKSS-%02d', $number),
            ]);
        }

        $this->get(route('ikss.index', [
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'per_page' => 5,
            'page' => 2,
        ]))
            ->assertOk()
            ->assertSee('value="5" selected', false)
            ->assertSee('Menampilkan 6–10 dari 12 data')
            ->assertSee('IKSS-06')
            ->assertSee('IKSS-10')
            ->assertDontSee('IKSS-01');
    }

    public function test_monitoring_rejects_an_unsupported_page_size(): void
    {
        $this->get(route('ikss.index', ['per_page' => 6]))
            ->assertRedirect()
            ->assertSessionHasErrors('per_page');
    }

    public function test_user_can_open_the_ikss_create_form(): void
    {
        $this->get(route('ikss.create', ['tahun' => 2026, 'triwulan' => 'TW1']))
            ->assertOk()
            ->assertSee('Tambah Indikator IKSS')
            ->assertSee('name="kode_indikator"', false);
    }

    public function test_user_can_open_the_ikss_edit_form(): void
    {
        $ikssMaster = IkssMaster::factory()->create();

        $this->get(route('ikss.edit', [
            'ikssMaster' => $ikssMaster,
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ]))
            ->assertOk()
            ->assertSee('Edit Indikator IKSS')
            ->assertSee($ikssMaster->kode_indikator);
    }

    public function test_user_can_open_the_capaian_form_for_a_selected_period(): void
    {
        $ikssMaster = IkssMaster::factory()->create();

        $this->get(route('ikss.capaian.edit', [
            'ikssMaster' => $ikssMaster,
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ]))
            ->assertOk()
            ->assertSee('Input Capaian Kinerja')
            ->assertSee('name="realisasi"', false);
    }

    public function test_monitoring_shows_delete_confirmation_for_ikss_without_history(): void
    {
        IkssMaster::factory()->create();

        $this->get(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']))
            ->assertOk()
            ->assertSee('Hapus IKSS?')
            ->assertSee('Data yang memiliki riwayat capaian tidak dapat dihapus.');
    }

    public function test_monitoring_uses_dialogs_for_ikss_and_capaian_forms(): void
    {
        $ikssMaster = IkssMaster::factory()->create();
        $indexUrl = route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']);

        $this->get($indexUrl)
            ->assertOk()
            ->assertSee('id="create-ikss-title"', false)
            ->assertSee('action="'.route('ikss.store').'"', false)
            ->assertSee('action="'.route('ikss.update', $ikssMaster).'"', false)
            ->assertSee('action="'.route('ikss.capaian.update', $ikssMaster).'"', false)
            ->assertSee('name="_dialog" value="ikss-create"', false)
            ->assertSee('name="_dialog" value="ikss-update-'.$ikssMaster->id.'"', false)
            ->assertSee('name="_dialog" value="capaian-'.$ikssMaster->id.'"', false)
            ->assertDontSee('href="'.route('ikss.create').'"', false)
            ->assertDontSee('href="'.route('ikss.edit', $ikssMaster).'"', false);
    }

    public function test_validation_errors_reopen_the_create_ikss_dialog(): void
    {
        $indexUrl = route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']);

        $this->from($indexUrl)->followingRedirects()->post(route('ikss.store'), [
            '_dialog' => 'ikss-create',
            'tahun' => 2026,
            'triwulan' => 'TW1',
            'kode_indikator' => '',
            'sasaran_kegiatan' => '',
            'indikator_kinerja' => '',
            'target' => null,
        ])
            ->assertOk()
            ->assertSee('x-init="$nextTick(() => $refs.createIkssDialog.showModal())"', false);
    }

    public function test_authenticated_users_can_create_update_and_delete_ikss(): void
    {
        $attributes = [
            'kode_indikator' => '7.1.1.1',
            'sasaran_kegiatan' => 'Sasaran awal',
            'indikator_kinerja' => 'Indikator awal',
            'target' => '80 Persen',
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ];

        $this->post(route('ikss.store'), $attributes)
            ->assertRedirect(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']));

        $ikssMaster = IkssMaster::query()->where('kode_indikator', '7.1.1.1')->firstOrFail();
        $this->put(route('ikss.update', $ikssMaster), [
            ...$attributes,
            'kode_indikator' => '7.1.1.2',
            'sasaran_kegiatan' => 'Sasaran diperbarui',
        ])->assertRedirect(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']));

        $this->assertDatabaseHas('ikss_masters', [
            'id' => $ikssMaster->id,
            'kode_indikator' => '7.1.1.2',
            'sasaran_kegiatan' => 'Sasaran diperbarui',
        ]);

        $this->delete(route('ikss.destroy', $ikssMaster), [
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ])->assertRedirect(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']));

        $this->assertDatabaseMissing('ikss_masters', ['id' => $ikssMaster->id]);
    }

    public function test_ikss_with_capaian_history_cannot_be_deleted(): void
    {
        $ikssMaster = IkssMaster::factory()->create();
        $periode = Periode::factory()->create([
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ]);
        CapaianKinerja::factory()->create([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $periode->id,
        ]);

        $this->delete(route('ikss.destroy', $ikssMaster), [
            'tahun' => 2026,
            'triwulan' => 'TW1',
        ])
            ->assertRedirect(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']))
            ->assertSessionHasErrors('ikss');

        $this->assertModelExists($ikssMaster);
        $this->assertDatabaseCount('capaian_kinerjas', 1);
    }

    public function test_authenticated_users_can_create_update_and_delete_capaian(): void
    {
        $ikssMaster = IkssMaster::factory()->create();
        $attributes = [
            'tahun' => 2026,
            'triwulan' => 'TW2',
            'target_pk' => '100.00',
            'target_tw' => '25.00',
            'realisasi' => '20.00',
            'capaian_persen' => '80.00',
            'capaian_tw_sebelumnya' => '75.00',
        ];

        $this->put(route('ikss.capaian.update', $ikssMaster), $attributes)
            ->assertRedirect(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW2']));

        $capaian = CapaianKinerja::query()
            ->where('ikss_master_id', $ikssMaster->id)
            ->firstOrFail();
        $this->assertDatabaseHas('capaian_kinerjas', [
            'id' => $capaian->id,
            'status' => 'Sudah Diisi',
            'updated_by' => $this->user->id,
        ]);

        $this->put(route('ikss.capaian.update', $ikssMaster), [
            ...$attributes,
            'capaian_persen' => null,
            'capaian_tw_sebelumnya' => null,
        ])->assertRedirect(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW2']));

        $this->assertDatabaseHas('capaian_kinerjas', [
            'id' => $capaian->id,
            'status' => 'Sebagian',
            'capaian_persen' => null,
        ]);

        $this->delete(route('capaian-kinerja.destroy', $capaian))
            ->assertRedirect(route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW2']));

        $this->assertDatabaseMissing('capaian_kinerjas', ['id' => $capaian->id]);
    }

    public function test_capaian_numeric_fields_are_validated_before_a_period_is_created(): void
    {
        $ikssMaster = IkssMaster::factory()->create();
        $indexUrl = route('ikss.index', ['tahun' => 2026, 'triwulan' => 'TW1']);

        $this->from($indexUrl)->followingRedirects()->put(route('ikss.capaian.update', $ikssMaster), [
            '_dialog' => 'capaian-'.$ikssMaster->id,
            'tahun' => 2026,
            'triwulan' => 'TW1',
            'target_pk' => null,
            'target_tw' => null,
            'realisasi' => 'not-a-number',
            'capaian_persen' => null,
            'capaian_tw_sebelumnya' => null,
        ])
            ->assertOk()
            ->assertSee('x-init="$nextTick(() => $refs.capaianDialog.showModal())"', false);

        $this->assertDatabaseCount('periodes', 0);
        $this->assertDatabaseCount('capaian_kinerjas', 0);
    }

    public function test_guest_cannot_manage_ikss(): void
    {
        auth()->logout();

        $this->post(route('ikss.store'))
            ->assertRedirect(route('login'));
    }
}
