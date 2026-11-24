<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DokumenController extends Controller
{
    private const MENUS = [
        'renstra' => [
            'title' => 'Renstra',
            'subtitle' => 'Rencana Strategis BPS Provinsi Kalimantan Timur',
        ],
        'perjanjian_kinerja' => [
            'title' => 'Perjanjian Kinerja',
            'subtitle' => 'Dokumen Komitmen Kinerja BPS Provinsi Kalimantan Timur',
        ],
    ];

    public function index(Request $request, string $menuKey): View
    {
        abort_unless(isset(self::MENUS[$menuKey]), 404);

        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:1900,2100'],
        ]);

        $selectedYear = isset($validated['tahun']) ? (int) $validated['tahun'] : null;
        $menu = self::MENUS[$menuKey];

        $years = Dokumen::query()
            ->where('menu_key', $menuKey)
            ->whereNotNull('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $documentsQuery = Dokumen::query()
            ->where('menu_key', $menuKey);

        if ($selectedYear !== null) {
            $documentsQuery->where('tahun', $selectedYear);
        }

        $documents = $documentsQuery
            ->orderByDesc('tahun')
            ->orderBy('judul')
            ->get();

        foreach ($documents as $document) {
            $document->setAttribute('document_url', $this->documentUrl($document));
        }

        return view('dokumens.index', [
            'documents' => $documents,
            'menuKey' => $menuKey,
            'pageTitle' => $menu['title'],
            'subtitle' => $menu['subtitle'],
            'selectedYear' => $selectedYear,
            'years' => $years,
        ]);
    }

    private function documentUrl(Dokumen $document): ?string
    {
        if ($document->jenis === 'File') {
            return filled($document->file_path)
                ? Storage::disk('public')->url($document->file_path)
                : null;
        }

        if ($document->jenis !== 'Link' || ! filter_var($document->url_link, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($document->url_link, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $document->url_link : null;
    }
}
