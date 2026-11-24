<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Jadwal;
use App\Models\Periode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = Carbon::parse(($validated['month'] ?? now()->format('Y-m')).'-01')->startOfMonth();
        $calendarStart = $month->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $activities = Jadwal::query()
            ->whereDate('tanggal_mulai', '<=', $calendarEnd->toDateString())
            ->whereDate('tanggal_selesai', '>=', $calendarStart->toDateString())
            ->orderBy('tanggal_mulai')
            ->get();

        $calendarDays = [];

        for ($date = $calendarStart->copy(); $date->lte($calendarEnd); $date->addDay()) {
            $calendarDays[] = [
                'date' => $date->copy(),
            ];
        }

        $calendarWeeks = [];

        foreach (collect($calendarDays)->chunk(7) as $days) {
            $weekStart = $days->first()['date'];
            $weekEnd = $days->last()['date'];
            $laneEnds = [];
            $bars = [];

            $weekActivities = $activities->filter(
                fn (Jadwal $activity): bool => $activity->tanggal_mulai->lte($weekEnd)
                    && $activity->tanggal_selesai->gte($weekStart)
            );

            foreach ($weekActivities as $activity) {
                $barStart = $activity->tanggal_mulai->lt($weekStart)
                    ? $weekStart
                    : $activity->tanggal_mulai;
                $barEnd = $activity->tanggal_selesai->gt($weekEnd)
                    ? $weekEnd
                    : $activity->tanggal_selesai;
                $startColumn = $barStart->dayOfWeekIso;
                $endColumn = $barEnd->dayOfWeekIso;
                $lane = 0;

                while (isset($laneEnds[$lane]) && $laneEnds[$lane] >= $startColumn) {
                    $lane++;
                }

                $laneEnds[$lane] = $endColumn;
                $bars[] = [
                    'activity' => $activity,
                    'startColumn' => $startColumn,
                    'columnSpan' => $endColumn - $startColumn + 1,
                    'lane' => $lane,
                ];
            }

            $occupiedColumns = [];

            foreach ($bars as $bar) {
                $lastColumn = $bar['startColumn'] + $bar['columnSpan'] - 1;

                for ($column = $bar['startColumn']; $column <= $lastColumn; $column++) {
                    $occupiedColumns[$column] = true;
                }
            }

            $days = $days->map(function (array $day) use ($occupiedColumns): array {
                $day['hasActivity'] = isset($occupiedColumns[$day['date']->dayOfWeekIso]);

                return $day;
            });

            $calendarWeeks[] = [
                'days' => $days,
                'bars' => $bars,
                'laneCount' => count($laneEnds),
            ];
        }

        $today = now()->startOfDay();
        $upcomingActivities = Jadwal::query()
            ->where('selesai', false)
            ->whereDate('tanggal_selesai', '>=', now()->toDateString())
            ->whereDate('tanggal_mulai', '<=', now()->addDays(30)->toDateString())
            ->orderBy('tanggal_mulai')
            ->get();

        $soonActivities = Jadwal::query()
            ->where('selesai', false)
            ->whereDate('tanggal_mulai', '>=', $today->toDateString())
            ->whereDate('tanggal_mulai', '<=', $today->copy()->addDays(7)->toDateString())
            ->orderBy('tanggal_mulai')
            ->get();

        $totalIndicators = IkssMaster::query()->count();
        $activePeriod = Periode::query()
            ->where('is_active', true)
            ->orderByDesc('tahun')
            ->orderByDesc('triwulan')
            ->first();
        $periodEntries = CapaianKinerja::query()
            ->where('periode_id', $activePeriod?->id ?? 0);
        $completedIndicators = $activePeriod
            ? (clone $periodEntries)->where('status', 'Sudah Diisi')->count()
            : 0;
        $inProgressIndicators = $activePeriod
            ? (clone $periodEntries)->where('status', 'Sebagian')->count()
            : 0;

        return view('dashboard', [
            'month' => $month,
            'calendarWeeks' => $calendarWeeks,
            'upcomingActivities' => $upcomingActivities,
            'soonActivities' => $soonActivities,
            'statusCounts' => [
                'notStarted' => max($totalIndicators - $completedIndicators - $inProgressIndicators, 0),
                'inProgress' => $inProgressIndicators,
                'completed' => $completedIndicators,
                'deadlines' => $upcomingActivities->count(),
            ],
        ]);
    }
}
