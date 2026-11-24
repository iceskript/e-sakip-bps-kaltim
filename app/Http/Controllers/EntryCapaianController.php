<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EntryCapaianController extends Controller
{
    private const STRATEGIC_OBJECTIVES = [
        'T1' => 'T1 — KEBIJAKAN BERBASIS DATA',
        'T2' => 'T2 — SISTEM STATISTIK NASIONAL',
        'T3' => 'T3 — TATA KELOLA BPS',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'triwulan' => ['nullable', Rule::in(Periode::QUARTERS)],
        ]);

        $activePeriod = Periode::query()
            ->where('is_active', true)
            ->orderByDesc('tahun')
            ->orderByDesc('triwulan')
            ->first();

        $tahun = (int) ($validated['tahun'] ?? $activePeriod?->tahun ?? now()->year);
        $triwulan = $validated['triwulan'] ?? $activePeriod?->triwulan ?? 'TW1';
        $triwulanLabel = match ($triwulan) {
            'TW1' => 'TW I',
            'TW2' => 'TW II',
            'TW3' => 'TW III',
            'TW4' => 'TW IV',
        };
        $periode = Periode::query()
            ->where('tahun', $tahun)
            ->where('triwulan', $triwulan)
            ->first();

        $dataIkss = IkssMaster::query()
            ->withExists('capaianKinerjas')
            ->with([
                'capaianKinerjas' => function (HasMany $query) use ($periode): void {
                    $query->where('periode_id', $periode?->id ?? 0)
                        ->with('updatedBy:id,name,username');
                },
            ])
            ->orderBy('kode_indikator')
            ->get();

        $indikators = $dataIkss->groupBy(
            fn (IkssMaster $ikss): string => $this->strategicObjective($ikss),
        );
        $totalIndikators = $dataIkss->count();
        $completedIndikators = $dataIkss->filter(
            fn (IkssMaster $ikss): bool => $ikss->capaianKinerjas->first()?->status === 'Sudah Diisi',
        )->count();

        return view('capaian-kinerja.index', [
            'indikators' => $indikators,
            'newIkssMaster' => new IkssMaster,
            'tahun' => $tahun,
            'triwulan' => $triwulan,
            'triwulanLabel' => $triwulanLabel,
            'years' => collect([2025, 2026, 2027, $tahun])->unique()->sort()->values(),
            'totalIndikators' => $totalIndikators,
            'completedIndikators' => $completedIndikators,
            'progressPercentage' => $totalIndikators > 0
                ? (int) round(($completedIndikators / $totalIndikators) * 100)
                : 0,
        ]);
    }

    public function update(Request $request, IkssMaster $ikssMaster): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
            'realisasi' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'link_bukti' => ['nullable', 'url:http,https', 'max:2048'],
            'kendala' => ['nullable', 'string', 'max:5000'],
        ]);

        $selection = [
            'tahun' => (int) $validated['tahun'],
            'triwulan' => $validated['triwulan'],
        ];
        $periode = Periode::query()->firstOrCreate(
            ['tahun' => $selection['tahun'], 'triwulan' => $selection['triwulan']],
            ['is_active' => false],
        );

        CapaianKinerja::query()->updateOrCreate(
            ['ikss_master_id' => $ikssMaster->id, 'periode_id' => $periode->id],
            [
                'realisasi' => $validated['realisasi'],
                'link_bukti' => $validated['link_bukti'] ?? null,
                'kendala' => $validated['kendala'] ?? null,
                'status' => 'Sudah Diisi',
                'updated_by' => $request->user()->id,
            ],
        );

        return to_route('capaian-kinerja.index', $selection)
            ->with('success', 'Capaian kinerja berhasil disimpan.');
    }

    private function strategicObjective(IkssMaster $ikssMaster): string
    {
        $strategicObjective = $ikssMaster->sasaran_strategis;

        if (is_string($strategicObjective) && isset(self::STRATEGIC_OBJECTIVES[$strategicObjective])) {
            return self::STRATEGIC_OBJECTIVES[$strategicObjective];
        }

        $legacyObjective = 'T'.explode('.', $ikssMaster->kode_indikator)[0];

        return self::STRATEGIC_OBJECTIVES[$legacyObjective]
            ?? $legacyObjective.' — SASARAN STRATEGIS LAINNYA';
    }
}
