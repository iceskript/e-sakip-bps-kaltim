<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IkssController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'triwulan' => ['nullable', Rule::in(Periode::QUARTERS)],
            'per_page' => ['nullable', 'integer', Rule::in([5, 10, 25, 50])],
        ]);

        $activePeriod = Periode::query()
            ->where('is_active', true)
            ->orderByDesc('tahun')
            ->orderByDesc('triwulan')
            ->first();

        $tahun = (int) ($validated['tahun'] ?? $activePeriod?->tahun ?? now()->year);
        $triwulan = $validated['triwulan'] ?? $activePeriod?->triwulan ?? 'TW1';
        $perPage = (int) ($validated['per_page'] ?? 10);
        $periode = Periode::query()
            ->where('tahun', $tahun)
            ->where('triwulan', $triwulan)
            ->first();

        $ikss = IkssMaster::query()
            ->withCount('capaianKinerjas')
            ->orderBy('kode_indikator')
            ->paginate($perPage)
            ->withQueryString();

        $capaianByIkss = $periode
            ? CapaianKinerja::query()
                ->where('periode_id', $periode->id)
                ->whereIn('ikss_master_id', $ikss->getCollection()->pluck('id'))
                ->get()
                ->keyBy('ikss_master_id')
            : collect();

        $totalCount = IkssMaster::query()->count();
        $statusCounts = $periode
            ? CapaianKinerja::query()
                ->where('periode_id', $periode->id)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
            : collect();

        $years = collect(range(max(2000, now()->year - 4), now()->year + 1))
            ->merge(Periode::query()
                ->select('tahun')
                ->distinct()
                ->orderByDesc('tahun')
                ->pluck('tahun'))
            ->push($tahun)
            ->unique()
            ->sortDesc()
            ->values();

        return view('ikss.index', [
            'ikss' => $ikss,
            'capaianByIkss' => $capaianByIkss,
            'statusCounts' => $statusCounts,
            'newIkssMaster' => new IkssMaster,
            'years' => $years,
            'tahun' => $tahun,
            'triwulan' => $triwulan,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
        ]);
    }

    public function create(Request $request): View
    {
        return view('ikss.form', [
            'ikssMaster' => new IkssMaster,
            ...$this->selection($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_indikator' => ['required', 'string', 'max:20', 'unique:ikss_masters,kode_indikator'],
            'sasaran_strategis' => ['nullable', Rule::in(['T1', 'T2', 'T3'])],
            'sasaran_kegiatan' => ['required', 'string'],
            'indikator_kinerja' => ['required', 'string'],
            'target' => ['nullable', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
            'return_to' => ['nullable', Rule::in(['entry'])],
        ]);

        $returnToEntry = ($validated['return_to'] ?? null) === 'entry';
        $selection = [
            'tahun' => (int) $validated['tahun'],
            'triwulan' => $validated['triwulan'],
        ];
        unset($validated['tahun'], $validated['triwulan'], $validated['return_to']);
        $validated['sasaran_strategis'] ??= $this->strategicObjectiveFromCode($validated['kode_indikator']);

        IkssMaster::create($validated);

        return to_route($returnToEntry ? 'capaian-kinerja.index' : 'ikss.index', $selection)
            ->with('success', 'Indikator IKSS berhasil ditambahkan.');
    }

    public function edit(Request $request, IkssMaster $ikssMaster): View
    {
        return view('ikss.form', [
            'ikssMaster' => $ikssMaster,
            ...$this->selection($request),
        ]);
    }

    public function update(Request $request, IkssMaster $ikssMaster): RedirectResponse
    {
        $validated = $request->validate([
            'kode_indikator' => [
                'required',
                'string',
                'max:20',
                Rule::unique('ikss_masters', 'kode_indikator')->ignore($ikssMaster),
            ],
            'sasaran_strategis' => ['nullable', Rule::in(['T1', 'T2', 'T3'])],
            'sasaran_kegiatan' => ['required', 'string'],
            'indikator_kinerja' => ['required', 'string'],
            'target' => ['nullable', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
        ]);

        $selection = [
            'tahun' => (int) $validated['tahun'],
            'triwulan' => $validated['triwulan'],
        ];
        unset($validated['tahun'], $validated['triwulan']);
        $validated['sasaran_strategis'] ??= $ikssMaster->sasaran_strategis
            ?? $this->strategicObjectiveFromCode($validated['kode_indikator']);

        $ikssMaster->update($validated);

        return to_route('ikss.index', $selection)->with('success', 'Indikator IKSS berhasil diperbarui.');
    }

    public function destroy(Request $request, IkssMaster $ikssMaster): RedirectResponse
    {
        $validated = $request->validate([
            'return_to' => ['nullable', Rule::in(['entry'])],
        ]);
        $selection = $this->selection($request);
        $redirectRoute = ($validated['return_to'] ?? null) === 'entry'
            ? 'capaian-kinerja.index'
            : 'ikss.index';

        if ($ikssMaster->capaianKinerjas()->exists()) {
            return to_route($redirectRoute, $selection)
                ->withErrors(['ikss' => 'IKSS tidak dapat dihapus karena sudah memiliki riwayat capaian.']);
        }

        $ikssMaster->delete();

        return to_route($redirectRoute, $selection)->with('success', 'Indikator IKSS berhasil dihapus.');
    }

    /**
     * @return array{tahun: int, triwulan: string}
     */
    private function selection(Request $request): array
    {
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'triwulan' => ['nullable', Rule::in(Periode::QUARTERS)],
        ]);

        return [
            'tahun' => (int) ($validated['tahun'] ?? now()->year),
            'triwulan' => $validated['triwulan'] ?? 'TW1',
        ];
    }

    private function strategicObjectiveFromCode(string $indicatorCode): ?string
    {
        $codePrefix = explode('.', $indicatorCode)[0];

        return in_array($codePrefix, ['1', '2', '3'], true) ? 'T'.$codePrefix : null;
    }
}
