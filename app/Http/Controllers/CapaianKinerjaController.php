<?php

namespace App\Http\Controllers;

use App\Models\CapaianKinerja;
use App\Models\IkssMaster;
use App\Models\Periode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CapaianKinerjaController extends Controller
{
    private const METRIC_FIELDS = [
        'target_pk',
        'target_tw',
        'realisasi',
        'capaian_persen',
        'capaian_tw_sebelumnya',
    ];

    public function edit(Request $request, IkssMaster $ikssMaster): View|JsonResponse
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

        if ($request->expectsJson()) {
            $previousSelection = $selection['triwulan'] === 'TW1'
                ? ['tahun' => $selection['tahun'] - 1, 'triwulan' => 'TW4']
                : [
                    'tahun' => $selection['tahun'],
                    'triwulan' => 'TW'.((int) substr($selection['triwulan'], 2) - 1),
                ];
            $previousPeriode = Periode::query()
                ->where('tahun', $previousSelection['tahun'])
                ->where('triwulan', $previousSelection['triwulan'])
                ->first();
            $previousCapaian = $previousPeriode
                ? CapaianKinerja::query()
                    ->where('ikss_master_id', $ikssMaster->id)
                    ->where('periode_id', $previousPeriode->id)
                    ->first()
                : null;
            $previousAnalysis = $previousCapaian?->analisis_capaian ?? [];
            $previousFollowUp = $previousCapaian?->tindak_lanjut ?? [];
            $previousQuarterLabel = [
                'TW1' => 'TW I',
                'TW2' => 'TW II',
                'TW3' => 'TW III',
                'TW4' => 'TW IV',
            ][$previousSelection['triwulan']];
            $capaianIku = $capaian?->capaian_iku;

            if ($capaian && ! $capaianIku) {
                $capaianIku = [
                    'tw1' => ['x' => 0, 'y' => 0],
                    'tw2' => ['x' => 0, 'y' => 0],
                    'tw3' => ['x' => 0, 'y' => 0],
                    'tw4' => ['x' => 0, 'y' => 0],
                ];
                $capaianIku[strtolower($selection['triwulan'])] = [
                    'x' => (float) ($capaian->realisasi ?? 0),
                    'y' => (float) ($capaian->target_pk ?? 0),
                ];
            }

            return response()->json([
                'data' => [
                    'ikss' => [
                        'id' => $ikssMaster->id,
                        'kode_indikator' => $ikssMaster->kode_indikator,
                        'sasaran_kegiatan' => $ikssMaster->sasaran_kegiatan,
                        'indikator_kinerja' => $ikssMaster->indikator_kinerja,
                        'target' => $ikssMaster->target,
                    ],
                    'periode' => $selection,
                    'capaian' => $capaian ? [
                        'id' => $capaian->id,
                        'target_pk' => $capaian->target_pk,
                        'target_tw' => $capaian->target_tw,
                        'capaian_iku' => $capaianIku,
                        'analisis_capaian' => $this->presentAttachments(
                            $capaian->analisis_capaian ?? [],
                            $capaian,
                        ),
                        'capaian_ro' => $this->presentAttachments(
                            $capaian->capaian_ro ?? [],
                            $capaian,
                        ),
                        'tindak_lanjut' => $capaian->tindak_lanjut,
                    ] : null,
                    'previous' => [
                        'label' => $previousQuarterLabel,
                        'tahun' => $previousSelection['tahun'],
                        'kendala' => $previousAnalysis['kendala'] ?? null,
                        'solusi' => $previousAnalysis['solusi'] ?? null,
                        'rencana_tindak_lanjut' => $previousAnalysis['rencana_tindak_lanjut'] ?? null,
                        'pic' => $previousAnalysis['pic'] ?? null,
                        'batas_waktu' => $previousAnalysis['batas_waktu'] ?? null,
                        'progres' => $previousFollowUp['progres_pelaksanaan'] ?? 0,
                    ],
                ],
            ]);
        }

        return view('ikss.capaian-form', [
            'ikssMaster' => $ikssMaster,
            'capaian' => $capaian,
            ...$selection,
        ]);
    }

    public function update(Request $request, IkssMaster $ikssMaster): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return $this->updateModalData($request, $ikssMaster);
        }

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

    public function downloadEvidence(CapaianKinerja $capaianKinerja, string $fileId): StreamedResponse
    {
        $file = $this->evidenceFiles($capaianKinerja)[$fileId] ?? null;

        abort_unless($file && Storage::disk('local')->exists($file['path']), 404);

        return Storage::disk('local')->download($file['path'], $file['name']);
    }

    public function destroy(CapaianKinerja $capaianKinerja): RedirectResponse
    {
        $selection = [
            'tahun' => $capaianKinerja->periode->tahun,
            'triwulan' => $capaianKinerja->periode->triwulan,
        ];

        foreach ($this->evidenceFiles($capaianKinerja) as $file) {
            Storage::disk('local')->delete($file['path']);
        }

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

    private function updateModalData(Request $request, IkssMaster $ikssMaster): JsonResponse
    {
        $payload = json_decode((string) $request->input('payload'), true);

        if (! is_array($payload)) {
            throw ValidationException::withMessages([
                'payload' => 'Format data capaian tidak valid.',
            ]);
        }

        if (is_array($payload['analisis_capaian'] ?? null)) {
            foreach (['batas_waktu', 'link_bukti_kinerja', 'link_bukti_tl_sebelumnya'] as $field) {
                if (($payload['analisis_capaian'][$field] ?? null) === '') {
                    $payload['analisis_capaian'][$field] = null;
                }
            }
        }

        $quarterRules = [];

        foreach (['tw1', 'tw2', 'tw3', 'tw4'] as $quarter) {
            $quarterRules["capaian_iku.{$quarter}"] = ['required', 'array:x,y'];
            $quarterRules["capaian_iku.{$quarter}.x"] = ['required', 'numeric', 'min:0', 'max:99999999.99'];
            $quarterRules["capaian_iku.{$quarter}.y"] = ['required', 'numeric', 'min:0', 'max:99999999.99'];
        }

        $validated = Validator::make($payload, [
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'triwulan' => ['required', Rule::in(Periode::QUARTERS)],
            'capaian_iku' => ['required', 'array:tw1,tw2,tw3,tw4'],
            ...$quarterRules,
            'analisis_capaian' => ['required', 'array:kendala,solusi,solution_points,rencana_tindak_lanjut,pic,batas_waktu,link_bukti_kinerja,link_bukti_tl_sebelumnya'],
            'analisis_capaian.kendala' => ['nullable', 'string', 'max:10000'],
            'analisis_capaian.solusi' => ['nullable', 'string', 'max:10000'],
            'analisis_capaian.solution_points' => ['present', 'array', 'max:30'],
            'analisis_capaian.solution_points.*' => ['array:uraian,photos'],
            'analisis_capaian.solution_points.*.uraian' => ['nullable', 'string', 'max:2000'],
            'analisis_capaian.solution_points.*.photos' => ['present', 'array', 'max:10'],
            'analisis_capaian.solution_points.*.photos.*' => ['array:id,name,url'],
            'analisis_capaian.rencana_tindak_lanjut' => ['nullable', 'string', 'max:10000'],
            'analisis_capaian.pic' => ['nullable', 'string', 'max:255'],
            'analisis_capaian.batas_waktu' => ['nullable', 'date'],
            'analisis_capaian.link_bukti_kinerja' => ['nullable', 'url:http,https', 'max:2048'],
            'analisis_capaian.link_bukti_tl_sebelumnya' => ['nullable', 'url:http,https', 'max:2048'],
            'capaian_ro' => ['required', 'array', 'size:2'],
            'capaian_ro.*' => ['required', 'array:kode,nama,vol,progres,narasi,fotos,laporan_pdf,kegiatan'],
            'capaian_ro.*.kode' => ['required', 'string', 'max:30'],
            'capaian_ro.*.nama' => ['required', 'string', 'max:500'],
            'capaian_ro.*.vol' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'capaian_ro.*.progres' => ['nullable', 'numeric', 'between:0,100'],
            'capaian_ro.*.narasi' => ['nullable', 'string', 'max:10000'],
            'capaian_ro.*.fotos' => ['present', 'array', 'max:20'],
            'capaian_ro.*.fotos.*' => ['array:id,name,url'],
            'capaian_ro.*.laporan_pdf' => ['nullable', 'array:id,name,url'],
            'capaian_ro.*.kegiatan' => ['present', 'array', 'max:30'],
            'capaian_ro.*.kegiatan.*' => ['array:kegiatan,sub_kegiatan,tahapan,fotos'],
            'capaian_ro.*.kegiatan.*.kegiatan' => ['nullable', 'string', 'max:2000'],
            'capaian_ro.*.kegiatan.*.sub_kegiatan' => ['nullable', 'string', 'max:2000'],
            'capaian_ro.*.kegiatan.*.tahapan' => ['nullable', 'string', 'max:5000'],
            'capaian_ro.*.kegiatan.*.fotos' => ['present', 'array', 'max:20'],
            'capaian_ro.*.kegiatan.*.fotos.*' => ['array:id,name,url'],
            'tindak_lanjut' => ['required', 'array:progres_pelaksanaan'],
            'tindak_lanjut.progres_pelaksanaan' => ['required', 'numeric', 'between:0,100'],
        ])->validate();

        Validator::make(['files' => $request->file('files', [])], [
            'files' => ['sometimes', 'array'],
            'files.analisis_capaian.solution_points' => ['sometimes', 'array'],
            'files.analisis_capaian.solution_points.*.photos' => ['sometimes', 'array'],
            'files.analisis_capaian.solution_points.*.photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'files.capaian_ro' => ['sometimes', 'array'],
            'files.capaian_ro.*.fotos' => ['sometimes', 'array'],
            'files.capaian_ro.*.fotos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'files.capaian_ro.*.laporan_pdf' => ['sometimes', 'file', 'mimes:pdf', 'max:20480'],
            'files.capaian_ro.*.kegiatan' => ['sometimes', 'array'],
            'files.capaian_ro.*.kegiatan.*.fotos' => ['sometimes', 'array'],
            'files.capaian_ro.*.kegiatan.*.fotos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ])->validate();

        $selection = [
            'tahun' => (int) $validated['tahun'],
            'triwulan' => $validated['triwulan'],
        ];
        $periode = Periode::query()->firstOrCreate(
            ['tahun' => $selection['tahun'], 'triwulan' => $selection['triwulan']],
            ['is_active' => false],
        );
        $capaian = CapaianKinerja::query()->firstOrNew([
            'ikss_master_id' => $ikssMaster->id,
            'periode_id' => $periode->id,
        ]);
        $existingFiles = $this->evidenceFiles($capaian);
        $validated = $this->preserveAttachments($validated, $existingFiles);
        $validated = $this->storeUploads($validated, $request->file('files', []));
        $quarterData = $validated['capaian_iku'][strtolower($selection['triwulan'])];
        $hasQuarterlyData = (float) $quarterData['y'] > 0;
        $achievement = $hasQuarterlyData
            ? round(((float) $quarterData['x'] / (float) $quarterData['y']) * 100, 2)
            : 0;

        DB::transaction(function () use ($capaian, $ikssMaster, $periode, $validated, $achievement, $quarterData, $hasQuarterlyData, $request): void {
            $capaian->fill([
                'ikss_master_id' => $ikssMaster->id,
                'periode_id' => $periode->id,
                'realisasi' => $hasQuarterlyData ? $quarterData['x'] : $capaian->realisasi,
                'capaian_persen' => $hasQuarterlyData ? $achievement : $capaian->capaian_persen,
                'kendala' => $validated['analisis_capaian']['kendala'] ?? null,
                'link_bukti' => $validated['analisis_capaian']['link_bukti_kinerja'] ?? null,
                'capaian_iku' => $validated['capaian_iku'],
                'analisis_capaian' => $validated['analisis_capaian'],
                'capaian_ro' => $validated['capaian_ro'],
                'tindak_lanjut' => $validated['tindak_lanjut'],
                'status' => 'Sudah Diisi',
                'updated_by' => $request->user()->id,
            ])->save();
        });

        $remainingFiles = $this->evidenceFiles($capaian);
        $removedFiles = array_diff_key($existingFiles, $remainingFiles);

        foreach ($removedFiles as $file) {
            Storage::disk('local')->delete($file['path']);
        }

        return response()->json([
            'message' => 'Data capaian berhasil disimpan.',
            'data' => [
                'capaian' => [
                    'id' => $capaian->id,
                    'capaian_iku' => $capaian->capaian_iku,
                    'analisis_capaian' => $this->presentAttachments($capaian->analisis_capaian ?? [], $capaian),
                    'capaian_ro' => $this->presentAttachments($capaian->capaian_ro ?? [], $capaian),
                    'tindak_lanjut' => $capaian->tindak_lanjut,
                ],
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, array{id: string, name: string, path: string}>  $existingFiles
     * @return array<string, mixed>
     */
    private function preserveAttachments(array $data, array $existingFiles): array
    {
        foreach ($data['analisis_capaian']['solution_points'] as $pointIndex => $point) {
            $data['analisis_capaian']['solution_points'][$pointIndex]['photos'] = $this->existingAttachments(
                $point['photos'],
                $existingFiles,
            );
        }

        foreach ($data['capaian_ro'] as $rowIndex => $row) {
            $data['capaian_ro'][$rowIndex]['fotos'] = $this->existingAttachments($row['fotos'], $existingFiles);
            $data['capaian_ro'][$rowIndex]['laporan_pdf'] = $row['laporan_pdf']
                ? $this->existingAttachment($row['laporan_pdf'], $existingFiles)
                : null;

            foreach ($row['kegiatan'] as $activityIndex => $activity) {
                $data['capaian_ro'][$rowIndex]['kegiatan'][$activityIndex]['fotos'] = $this->existingAttachments(
                    $activity['fotos'],
                    $existingFiles,
                );
            }
        }

        return $data;
    }

    /**
     * @param  array<int, array{id: string, name: string, url?: string}>  $attachments
     * @param  array<string, array{id: string, name: string, path: string}>  $existingFiles
     * @return array<int, array{id: string, name: string, path: string}>
     */
    private function existingAttachments(array $attachments, array $existingFiles): array
    {
        return array_map(
            fn (array $attachment): array => $this->existingAttachment($attachment, $existingFiles),
            $attachments,
        );
    }

    /**
     * @param  array{id: string, name: string, url?: string}  $attachment
     * @param  array<string, array{id: string, name: string, path: string}>  $existingFiles
     * @return array{id: string, name: string, path: string}
     */
    private function existingAttachment(array $attachment, array $existingFiles): array
    {
        $existing = $existingFiles[$attachment['id']] ?? null;

        if (! $existing) {
            throw ValidationException::withMessages([
                'files' => 'Salah satu lampiran tidak lagi tersedia. Muat ulang data sebelum menyimpan.',
            ]);
        }

        return $existing;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $files
     * @return array<string, mixed>
     */
    private function storeUploads(array $data, array $files): array
    {
        foreach ($files['analisis_capaian']['solution_points'] ?? [] as $pointIndex => $pointFiles) {
            if (! isset($data['analisis_capaian']['solution_points'][$pointIndex])) {
                throw ValidationException::withMessages([
                    'files' => 'Lampiran tidak cocok dengan poin solusi yang tersedia.',
                ]);
            }

            foreach ($pointFiles['photos'] ?? [] as $file) {
                $data['analisis_capaian']['solution_points'][$pointIndex]['photos'][] = $this->storeEvidence($file);
            }
        }

        foreach ($files['capaian_ro'] ?? [] as $rowIndex => $rowFiles) {
            if (! isset($data['capaian_ro'][$rowIndex])) {
                throw ValidationException::withMessages([
                    'files' => 'Lampiran tidak cocok dengan rincian output yang tersedia.',
                ]);
            }

            foreach ($rowFiles['fotos'] ?? [] as $file) {
                $data['capaian_ro'][$rowIndex]['fotos'][] = $this->storeEvidence($file);
            }

            if (isset($rowFiles['laporan_pdf'])) {
                $data['capaian_ro'][$rowIndex]['laporan_pdf'] = $this->storeEvidence($rowFiles['laporan_pdf']);
            }

            foreach ($rowFiles['kegiatan'] ?? [] as $activityIndex => $activityFiles) {
                if (! isset($data['capaian_ro'][$rowIndex]['kegiatan'][$activityIndex])) {
                    throw ValidationException::withMessages([
                        'files' => 'Lampiran tidak cocok dengan kegiatan yang tersedia.',
                    ]);
                }

                foreach ($activityFiles['fotos'] ?? [] as $file) {
                    $data['capaian_ro'][$rowIndex]['kegiatan'][$activityIndex]['fotos'][] = $this->storeEvidence($file);
                }
            }
        }

        return $data;
    }

    /**
     * @return array{id: string, name: string, path: string}
     */
    private function storeEvidence(UploadedFile $file): array
    {
        $id = (string) Str::uuid();
        $path = $file->storeAs('capaian-kinerja', $id.'.'.$file->guessExtension(), 'local');

        if (! is_string($path)) {
            throw new \RuntimeException('Lampiran gagal disimpan.');
        }

        return [
            'id' => $id,
            'name' => $file->getClientOriginalName(),
            'path' => $path,
        ];
    }

    /**
     * @return array<string, array{id: string, name: string, path: string}>
     */
    private function evidenceFiles(?CapaianKinerja $capaian): array
    {
        if (! $capaian) {
            return [];
        }

        $files = [];
        $visit = function (mixed $value) use (&$visit, &$files): void {
            if (! is_array($value)) {
                return;
            }

            if (isset($value['id'], $value['name'], $value['path'])) {
                $files[$value['id']] = $value;

                return;
            }

            foreach ($value as $item) {
                $visit($item);
            }
        };
        $visit($capaian->analisis_capaian ?? []);
        $visit($capaian->capaian_ro ?? []);

        return $files;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function presentAttachments(array $data, CapaianKinerja $capaian): array
    {
        $present = fn (array $attachment): array => [
            'id' => $attachment['id'],
            'name' => $attachment['name'],
            'url' => route('capaian-kinerja.files.download', [$capaian, $attachment['id']]),
        ];
        $map = fn (array $attachments): array => array_map($present, $attachments);

        if (isset($data['solution_points'])) {
            foreach ($data['solution_points'] as $index => $point) {
                $data['solution_points'][$index]['photos'] = $map($point['photos'] ?? []);
            }
        }

        foreach ($data as $rowIndex => $row) {
            if (! is_array($row) || ! isset($row['fotos'])) {
                continue;
            }

            $data[$rowIndex]['fotos'] = $map($row['fotos']);
            $data[$rowIndex]['laporan_pdf'] = isset($row['laporan_pdf'])
                ? $present($row['laporan_pdf'])
                : null;

            foreach ($row['kegiatan'] ?? [] as $activityIndex => $activity) {
                $data[$rowIndex]['kegiatan'][$activityIndex]['fotos'] = $map($activity['fotos'] ?? []);
            }
        }

        return $data;
    }
}
