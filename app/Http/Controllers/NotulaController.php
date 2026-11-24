<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class NotulaController extends Controller
{
    private const QUARTER_LABELS = [
        'TW1' => 'Triwulan I',
        'TW2' => 'Triwulan II',
        'TW3' => 'Triwulan III',
        'TW4' => 'Triwulan IV',
    ];

    private const PREVIEW_HEADERS = [
        'KODE',
        'INDIKATOR',
        'TARGET PK',
        'TARGET TW',
        'REALISASI',
        'CAP. TW',
        'CAP. PK',
        'STATUS',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'triwulan' => ['nullable', Rule::in(Periode::QUARTERS)],
            'source' => ['nullable', Rule::in(['app', 'excel'])],
        ]);

        $activePeriod = Periode::query()
            ->where('is_active', true)
            ->orderByDesc('tahun')
            ->orderByRaw("CASE triwulan WHEN 'TW1' THEN 1 WHEN 'TW2' THEN 2 WHEN 'TW3' THEN 3 WHEN 'TW4' THEN 4 ELSE 0 END DESC")
            ->first();

        $selectedYear = (int) ($validated['tahun'] ?? $activePeriod?->tahun ?? now()->year);
        $selectedQuarter = $validated['triwulan'] ?? $activePeriod?->triwulan ?? 'TW1';
        $periodId = Periode::query()
            ->where('tahun', $selectedYear)
            ->where('triwulan', $selectedQuarter)
            ->value('id');

        $years = Periode::query()
            ->select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(static fn ($year): int => (int) $year)
            ->push($selectedYear, now()->year, now()->year - 1)
            ->unique()
            ->sortDesc()
            ->values();

        $ikssData = $this->previewData($periodId);
        $counts = [
            'read' => $ikssData->count(),
            'complete' => $ikssData->where('status', 'Lengkap')->count(),
            'partial' => $ikssData->where('status', 'Sebagian')->count(),
            'empty' => $ikssData->where('status', 'Kosong')->count(),
        ];

        return view('notula.index', [
            'ikssData' => $ikssData,
            'counts' => $counts,
            'years' => $years,
            'quarters' => self::QUARTER_LABELS,
            'activePeriod' => $activePeriod,
            'selectedYear' => $selectedYear,
            'selectedQuarter' => $selectedQuarter,
            'selectedSource' => $validated['source'] ?? 'app',
            'meetingInfo' => session('notula.rapat', []),
            'uploadedExcelName' => session('notula.upload.name'),
            'phpVersion' => PHP_VERSION,
            'uploadLimit' => ini_get('upload_max_filesize') ?: 'tidak diketahui',
        ]);
    }

    public function storeRapat(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
            'satuan_kerja' => ['required', 'string', 'max:255'],
            'nilai_sakip' => ['nullable', 'string', 'max:100'],
            'predikat' => ['nullable', 'string', 'max:100'],
            'tanggal_rapat' => ['nullable', 'date'],
            'waktu' => ['nullable', 'string', 'max:100'],
            'tempat' => ['nullable', 'string', 'max:255'],
            'pimpinan_rapat' => ['nullable', 'string', 'max:255'],
            'nama_kepala_satker' => ['nullable', 'string', 'max:255'],
            'nama_notulis' => ['nullable', 'string', 'max:255'],
            'kota' => ['nullable', 'string', 'max:100'],
        ]);

        $request->session()->put('notula.rapat', [
            'satuan_kerja' => $validated['satuan_kerja'],
            'nilai_sakip' => $validated['nilai_sakip'] ?? '',
            'predikat' => $validated['predikat'] ?? '',
            'tanggal_rapat' => $validated['tanggal_rapat'] ?? '',
            'waktu' => $validated['waktu'] ?? '',
            'tempat' => $validated['tempat'] ?? '',
            'pimpinan_rapat' => $validated['pimpinan_rapat'] ?? '',
            'nama_kepala_satker' => $validated['nama_kepala_satker'] ?? '',
            'nama_notulis' => $validated['nama_notulis'] ?? '',
            'kota' => $validated['kota'] ?? '',
        ]);

        return to_route('notula.index', [
            'tahun' => $validated['tahun'],
            'triwulan' => $validated['triwulan'],
            'source' => 'app',
        ])->with('success', 'Informasi rapat berhasil disimpan.');
    }

    public function uploadExcel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
            'file' => ['required', 'file', 'mimes:xlsx', 'extensions:xlsx', 'max:1048576'],
        ]);

        $uploadedFile = $request->file('file');
        $previousUploadPath = $request->session()->get('notula.upload.path');

        if (is_string($previousUploadPath) && Storage::disk('local')->exists($previousUploadPath)) {
            if (! Storage::disk('local')->delete($previousUploadPath)) {
                throw new RuntimeException('File Excel sebelumnya gagal dihapus.');
            }
        }

        $filePath = $uploadedFile->store('notula/imports', 'local');

        if (! is_string($filePath)) {
            throw new RuntimeException('File Excel gagal disimpan.');
        }

        $request->session()->put('notula.upload', [
            'path' => $filePath,
            'name' => $uploadedFile->getClientOriginalName(),
        ]);

        return to_route('notula.index', [
            'tahun' => $validated['tahun'],
            'triwulan' => $validated['triwulan'],
            'source' => 'excel',
        ])->with('success', 'File Excel berhasil diunggah dan disimpan secara privat.');
    }

    public function generate(Request $request): BinaryFileResponse
    {
        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
            'format' => ['required', Rule::in(['docx', 'xlsx'])],
        ]);

        $year = (int) $validated['tahun'];
        $quarter = $validated['triwulan'];
        $periodId = Periode::query()
            ->where('tahun', $year)
            ->where('triwulan', $quarter)
            ->value('id');
        $ikssData = $this->previewData($periodId);
        $filename = 'notula-monitoring-'.$year.'-'.strtolower($quarter).'.'.$validated['format'];

        if ($validated['format'] === 'xlsx') {
            return $this->downloadOfficeFile(
                $this->spreadsheetFiles($ikssData),
                'kertas-kerja-'.$year.'-'.strtolower($quarter).'.xlsx',
            );
        }

        return $this->downloadOfficeFile(
            $this->wordDocumentFiles($ikssData, $year, $quarter, session('notula.rapat', [])),
            $filename,
        );
    }

    /**
     * @return Collection<int, array{
     *     kode: string,
     *     indikator: string,
     *     target_pk: string,
     *     target_tw: string,
     *     realisasi: string,
     *     cap_tw: string,
     *     cap_pk: string,
     *     status: string
     * }>
     */
    private function previewData(?int $periodId): Collection
    {
        return IkssMaster::query()
            ->with([
                'capaianKinerjas' => static function (HasMany $query) use ($periodId): void {
                    $query->where('periode_id', $periodId ?? 0);
                },
            ])
            ->orderBy('kode_indikator')
            ->get()
            ->map(function (IkssMaster $ikssMaster): array {
                /** @var CapaianKinerja|null $capaian */
                $capaian = $ikssMaster->capaianKinerjas->first();

                return [
                    'kode' => $ikssMaster->kode_indikator,
                    'indikator' => $ikssMaster->indikator_kinerja,
                    'target_pk' => $this->formatMetric($capaian?->target_pk ?? $ikssMaster->target),
                    'target_tw' => $this->formatMetric($capaian?->target_tw),
                    'realisasi' => $this->formatMetric($capaian?->realisasi),
                    'cap_tw' => $this->formatPercentage($capaian?->capaian_persen),
                    'cap_pk' => $this->calculatePercentage($capaian?->realisasi, $capaian?->target_pk),
                    'status' => match ($capaian?->status) {
                        'Sudah Diisi' => 'Lengkap',
                        'Sebagian' => 'Sebagian',
                        default => 'Kosong',
                    },
                ];
            });
    }

    private function formatMetric(mixed $value): string
    {
        if (! is_numeric($value)) {
            return filled($value) ? (string) $value : '—';
        }

        return rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',');
    }

    private function formatPercentage(mixed $value): string
    {
        return is_numeric($value)
            ? $this->formatMetric($value).'%'
            : '—';
    }

    private function calculatePercentage(mixed $value, mixed $target): string
    {
        if (! is_numeric($value) || ! is_numeric($target) || (float) $target === 0.0) {
            return '—';
        }

        return $this->formatPercentage(((float) $value / (float) $target) * 100);
    }

    /**
     * @param  Collection<int, array<string, string>>  $ikssData
     * @return array<string, string>
     */
    private function wordDocumentFiles(Collection $ikssData, int $year, string $quarter, array $meetingInfo): array
    {
        $paragraphs = [
            $this->wordParagraph('NOTULA MONITORING KINERJA', true),
            $this->wordParagraph('Tahun '.$year.' — '.(self::QUARTER_LABELS[$quarter] ?? $quarter), true),
        ];

        foreach ([
            'Satuan Kerja' => $meetingInfo['satuan_kerja'] ?? 'BPS Provinsi Kalimantan Timur',
            'Nilai SAKIP' => $meetingInfo['nilai_sakip'] ?? '',
            'Predikat' => $meetingInfo['predikat'] ?? '',
            'Hari/Tanggal Rapat' => $meetingInfo['tanggal_rapat'] ?? '',
            'Waktu' => $meetingInfo['waktu'] ?? '',
            'Tempat' => $meetingInfo['tempat'] ?? '',
            'Pimpinan Rapat' => $meetingInfo['pimpinan_rapat'] ?? '',
            'Nama Kepala Satker' => $meetingInfo['nama_kepala_satker'] ?? '',
            'Nama Notulis' => $meetingInfo['nama_notulis'] ?? '',
            'Kota (blok tanda tangan)' => $meetingInfo['kota'] ?? '',
        ] as $label => $value) {
            $paragraphs[] = $this->wordParagraph($label.': '.(filled($value) ? $value : '—'));
        }

        $tableRows = [self::PREVIEW_HEADERS];

        foreach ($ikssData as $item) {
            $tableRows[] = array_map(
                static fn (string $key): string => $item[$key] ?? '—',
                ['kode', 'indikator', 'target_pk', 'target_tw', 'realisasi', 'cap_tw', 'cap_pk', 'status'],
            );
        }

        $table = $this->wordTable($tableRows);
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            .'<w:body>'.implode('', $paragraphs).$table
            .'<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1134" w:right="850" w:bottom="1134" w:left="850"/></w:sectPr>'
            .'</w:body></w:document>';

        return [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
                .'</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
                .'</Relationships>',
            'word/document.xml' => $xml,
        ];
    }

    private function wordParagraph(string $text, bool $bold = false): string
    {
        $runProperties = $bold ? '<w:rPr><w:b/></w:rPr>' : '';

        return '<w:p><w:r>'.$runProperties.'<w:t xml:space="preserve">'.$this->escapeXml($text).'</w:t></w:r></w:p>';
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function wordTable(array $rows): string
    {
        $tableRows = '';

        foreach ($rows as $rowIndex => $row) {
            $cells = '';

            foreach ($row as $value) {
                $bold = $rowIndex === 0 ? '<w:rPr><w:b/></w:rPr>' : '';
                $cells .= '<w:tc><w:tcPr><w:tcW w:w="1350" w:type="dxa"/></w:tcPr>'
                    .'<w:p><w:r>'.$bold.'<w:t xml:space="preserve">'.$this->escapeXml($value).'</w:t></w:r></w:p></w:tc>';
            }

            $tableRows .= '<w:tr>'.$cells.'</w:tr>';
        }

        return '<w:tbl><w:tblPr><w:tblBorders>'
            .'<w:top w:val="single" w:sz="4" w:color="808080"/>'
            .'<w:left w:val="single" w:sz="4" w:color="808080"/>'
            .'<w:bottom w:val="single" w:sz="4" w:color="808080"/>'
            .'<w:right w:val="single" w:sz="4" w:color="808080"/>'
            .'<w:insideH w:val="single" w:sz="4" w:color="808080"/>'
            .'<w:insideV w:val="single" w:sz="4" w:color="808080"/>'
            .'</w:tblBorders></w:tblPr><w:tblGrid>'.str_repeat('<w:gridCol w:w="1350"/>', 8).'</w:tblGrid>'
            .$tableRows.'</w:tbl>';
    }

    /**
     * @param  Collection<int, array<string, string>>  $ikssData
     * @return array<string, string>
     */
    private function spreadsheetFiles(Collection $ikssData): array
    {
        $rows = [self::PREVIEW_HEADERS];

        foreach ($ikssData as $item) {
            $rows[] = array_map(
                static fn (string $key): string => $item[$key] ?? '—',
                ['kode', 'indikator', 'target_pk', 'target_tw', 'realisasi', 'cap_tw', 'cap_pk', 'status'],
            );
        }

        $sheetRows = '';

        foreach ($rows as $rowIndex => $row) {
            $cells = '';

            foreach (array_values($row) as $columnIndex => $value) {
                $address = chr(ord('A') + $columnIndex).($rowIndex + 1);
                $cells .= '<c r="'.$address.'" t="inlineStr"><is><t xml:space="preserve">'
                    .$this->escapeXml((string) $value)
                    .'</t></is></c>';
            }

            $sheetRows .= '<row r="'.($rowIndex + 1).'">'.$cells.'</row>';
        }

        return [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                .'<sheets><sheet name="Kertas Kerja" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'</Relationships>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'
                .$sheetRows
                .'</sheetData></worksheet>',
        ];
    }

    private function downloadOfficeFile(array $files, string $filename): BinaryFileResponse
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'notula-');

        if (! is_string($temporaryPath)) {
            throw new RuntimeException('File unduhan tidak dapat disiapkan.');
        }

        $archive = new ZipArchive;
        $openResult = $archive->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($openResult !== true) {
            unlink($temporaryPath);
            throw new RuntimeException('Arsip dokumen tidak dapat dibuat.');
        }

        foreach ($files as $path => $content) {
            if (! $archive->addFromString($path, $content)) {
                $archive->close();
                unlink($temporaryPath);
                throw new RuntimeException('Isi dokumen gagal ditambahkan ke arsip.');
            }
        }

        if (! $archive->close()) {
            unlink($temporaryPath);
            throw new RuntimeException('Arsip dokumen gagal diselesaikan.');
        }

        return response()->download($temporaryPath, $filename)->deleteFileAfterSend(true);
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
