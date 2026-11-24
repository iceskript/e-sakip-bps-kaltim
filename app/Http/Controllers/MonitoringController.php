<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\Dokumen;
use App\Models\IkssMaster;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    private const MENU_KEY = 'monitoring_kinerja';

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', Rule::in([2026, 2025])],
            'triwulan' => ['nullable', Rule::in(Periode::QUARTERS)],
        ]);

        $selectedYear = isset($validated['tahun']) ? (int) $validated['tahun'] : null;
        $selectedQuarter = $validated['triwulan'] ?? null;

        $monitoringQuery = Dokumen::query()->where('menu_key', self::MENU_KEY);

        if ($selectedYear !== null) {
            $monitoringQuery->where('tahun', $selectedYear);
        }

        if ($selectedQuarter !== null) {
            $monitoringQuery->where('triwulan', $selectedQuarter);
        }

        $monitoring = $monitoringQuery
            ->orderByDesc('tahun')
            ->orderBy('triwulan')
            ->orderBy('judul')
            ->get();

        foreach ($monitoring as $item) {
            $item->setAttribute('document_url', $this->documentUrl($item));
        }

        $years = collect([2026, 2025]);

        $ikssMasters = IkssMaster::query()
            ->orderBy('kode_indikator')
            ->get();

        $capaianByIkss = CapaianKinerja::query()
            ->join('periodes', 'capaian_kinerjas.periode_id', '=', 'periodes.id')
            ->select('capaian_kinerjas.*')
            ->when(
                $selectedYear !== null,
                fn ($query) => $query->where('periodes.tahun', $selectedYear),
            )
            ->when(
                $selectedQuarter !== null,
                fn ($query) => $query->where('periodes.triwulan', $selectedQuarter),
            )
            ->orderByDesc('periodes.tahun')
            ->orderByRaw("CASE periodes.triwulan WHEN 'TW1' THEN 1 WHEN 'TW2' THEN 2 WHEN 'TW3' THEN 3 WHEN 'TW4' THEN 4 ELSE 0 END DESC")
            ->get()
            ->unique('ikss_master_id')
            ->keyBy('ikss_master_id');

        foreach ($capaianByIkss as $capaian) {
            $capaian->setAttribute('evidence_url', $this->httpUrl($capaian->link_bukti));
        }

        return view('monitoring.index', [
            'monitoring' => $monitoring,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'selectedQuarter' => $selectedQuarter,
            'ikssMasters' => $ikssMasters,
            'capaianByIkss' => $capaianByIkss,
        ]);
    }

    private function httpUrl(?string $url): ?string
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }

    private function documentUrl(Dokumen $document): ?string
    {
        if ($document->jenis === 'File') {
            return filled($document->file_path)
                ? Storage::disk('public')->url($document->file_path)
                : null;
        }

        if ($document->jenis !== 'Link') {
            return null;
        }

        return $this->httpUrl($document->url_link);
    }
}
