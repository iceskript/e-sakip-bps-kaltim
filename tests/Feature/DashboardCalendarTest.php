<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        config(['services.google.allowed_emails' => [strtolower($user->email)]]);

        $this->actingAs($user);
    }

    public function test_dashboard_displays_saved_calendar_activities(): void
    {
        Jadwal::create([
            'judul' => 'Rapat koordinasi',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-18',
            'tanggal_selesai' => '2026-11-18',
        ]);

        $this->get(route('dashboard', ['month' => '2026-11']))
            ->assertOk()
            ->assertSee('Rapat koordinasi')
            ->assertSee('Renstra');
    }

    public function test_multi_day_activity_occupies_one_long_bar_for_its_week(): void
    {
        $activity = Jadwal::create([
            'judul' => 'Rapat koordinasi',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-18',
            'tanggal_selesai' => '2026-11-20',
        ]);

        $this->get(route('dashboard', ['month' => '2026-11']))
            ->assertViewHas('calendarWeeks', function (array $weeks) use ($activity): bool {
                foreach ($weeks as $week) {
                    foreach ($week['bars'] as $bar) {
                        if ($bar['activity']->is($activity)) {
                            return $bar['startColumn'] === 3 && $bar['columnSpan'] === 3;
                        }
                    }
                }

                return false;
            });
    }

    public function test_activity_can_be_created_and_is_saved(): void
    {
        $response = $this->post(route('jadwals.store'), [
            'judul' => 'Rapat koordinasi',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-18',
            'tanggal_selesai' => '2026-11-19',
            'deskripsi' => 'Pembahasan target tahunan',
        ]);

        $response->assertRedirect(route('dashboard', ['month' => '2026-11']));
        $this->assertDatabaseHas('jadwals', [
            'judul' => 'Rapat koordinasi',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-18 00:00:00',
            'tanggal_selesai' => '2026-11-19 00:00:00',
            'deskripsi' => 'Pembahasan target tahunan',
        ]);
    }

    public function test_activity_cannot_end_before_it_starts(): void
    {
        $response = $this->from(route('dashboard'))->post(route('jadwals.store'), [
            'judul' => 'Rapat koordinasi',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-19',
            'tanggal_selesai' => '2026-11-18',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('tanggal_selesai');
        $this->assertDatabaseCount('jadwals', 0);
    }

    public function test_activity_can_be_removed(): void
    {
        $activity = Jadwal::create([
            'judul' => 'Rapat koordinasi',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-18',
            'tanggal_selesai' => '2026-11-18',
        ]);

        $this->from(route('dashboard', ['month' => '2026-11']))
            ->delete(route('jadwals.destroy', $activity))
            ->assertRedirect(route('dashboard', ['month' => '2026-11']));

        $this->assertDatabaseMissing('jadwals', ['id' => $activity->id]);
    }

    public function test_activity_can_be_marked_complete_and_reopened(): void
    {
        $activity = Jadwal::create([
            'judul' => 'Rapat koordinasi',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-18',
            'tanggal_selesai' => '2026-11-18',
        ]);

        $this->patch(route('jadwals.completion.update', $activity), ['selesai' => true])
            ->assertRedirect();
        $this->assertDatabaseHas('jadwals', ['id' => $activity->id, 'selesai' => true]);

        $this->patch(route('jadwals.completion.update', $activity), ['selesai' => false])
            ->assertRedirect();
        $this->assertDatabaseHas('jadwals', ['id' => $activity->id, 'selesai' => false]);
    }

    public function test_dashboard_lists_unfinished_activities_starting_within_seven_days(): void
    {
        $this->travelTo(Carbon::parse('2026-11-19 09:00:00'));

        Jadwal::create([
            'judul' => 'Kegiatan segera',
            'kategori' => 'Renstra',
            'tanggal_mulai' => '2026-11-20',
            'tanggal_selesai' => '2026-11-20',
        ]);
        Jadwal::create([
            'judul' => 'Kegiatan nanti',
            'kategori' => 'LAKIN',
            'tanggal_mulai' => '2026-11-27',
            'tanggal_selesai' => '2026-11-27',
        ]);
        Jadwal::create([
            'judul' => 'Kegiatan selesai',
            'kategori' => 'FRA',
            'tanggal_mulai' => '2026-11-21',
            'tanggal_selesai' => '2026-11-21',
            'selesai' => true,
        ]);

        $this->get(route('dashboard', ['month' => '2026-11']))
            ->assertOk()
            ->assertSee('Agenda segera')
            ->assertViewHas('soonActivities', function ($activities): bool {
                return $activities->pluck('judul')->all() === ['Kegiatan segera'];
            });
    }
}
