<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CapaianKinerjaController extends Controller
{
    private const METRIC_FIELDS = [
        'target_pk',
        'target_tw',
        'realisasi',
        'capaian_persen',
        'capaian_tw_sebelumnya',
    ];

    public function edit(Request $request, IkssMaster $ikssMaster): View
    {
        $selection = $this->selection($request);
        $periode = Periode::query()
            ->where('tahun', $selection['tahun'])
            ->where('triwulan', $selection['triwulan'])
            ->first();

        $capaian = $periode
            ? CapaianKinerja::query()
                ->where('ikss_master_id', $ikssMaster->id)
                ->where('periode_id', $periode->id)
                ->first()
            : null;

        return view('ikss.capaian-form', [
            'ikssMaster' => $ikssMaster,
            'capaian' => $capaian,
            ...$selection,
        ]);
    }

    public function update(Request $request, IkssMaster $ikssMaster): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
            'target_pk' => ['present', 'nullable', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'target_tw' => ['present', 'nullable', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'realisasi' => ['present', 'nullable', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'capaian_persen' => ['present', 'nullable', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
            'capaian_tw_sebelumnya' => ['present', 'nullable', 'numeric', 'min:0', 'max:99999999.99', 'decimal:0,2'],
        ]);

        $selection = [
            'tahun' => (int) $validated['tahun'],
            'triwulan' => $validated['triwulan'],
        ];
        $metrics = Arr::only($validated, self::METRIC_FIELDS);
        $filledMetricCount = count(array_filter($metrics, fn ($value): bool => $value !== null));
        $status = match ($filledMetricCount) {
            0 => 'Kosong',
            count(self::METRIC_FIELDS) => 'Sudah Diisi',
            default => 'Sebagian',
        };

        $periode = Periode::query()->firstOrCreate(
            ['tahun' => $selection['tahun'], 'triwulan' => $selection['triwulan']],
            ['is_active' => false],
        );

        CapaianKinerja::query()->updateOrCreate(
            ['ikss_master_id' => $ikssMaster->id, 'periode_id' => $periode->id],
            [...$metrics, 'status' => $status, 'updated_by' => $request->user()->id],
        );

        return to_route('ikss.index', $selection)->with('success', 'Capaian kinerja berhasil disimpan.');
    }

    public function destroy(CapaianKinerja $capaianKinerja): RedirectResponse
    {
        $selection = [
            'tahun' => $capaianKinerja->periode->tahun,
            'triwulan' => $capaianKinerja->periode->triwulan,
        ];

        $capaianKinerja->delete();

        return to_route('ikss.index', $selection)->with('success', 'Data capaian berhasil dihapus.');
    }

    /**
     * @return array{tahun: int, triwulan: string}
     */
    private function selection(Request $request): array
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
        ]);

        return [
            'tahun' => (int) $validated['tahun'],
            'triwulan' => $validated['triwulan'],
        ];
    }
}
