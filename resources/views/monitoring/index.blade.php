@extends('layouts.app')

@section('title', 'Monitoring Capaian Kinerja')

@section('content')
    @php
        $yearFilterQuery = $selectedQuarter === null ? [] : ['triwulan' => $selectedQuarter];
        $quarterFilterQuery = $selectedYear === null ? [] : ['tahun' => $selectedYear];
        $quarterLabels = [
            'TW1' => 'TW I',
            'TW2' => 'TW II',
            'TW3' => 'TW III',
            'TW4' => 'TW IV',
        ];
    @endphp

    <header class="mb-6 flex items-center gap-4">
        <div class="grid size-12 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3v18h18M7 14l4-4 4 4 6-7" />
            </svg>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Monitoring Capaian Kinerja</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Laporan Monitoring Periodik</p>
        </div>
    </header>

    <section class="mb-5 flex flex-col gap-3" aria-label="Filter monitoring">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <h3 class="w-24 shrink-0 text-sm font-semibold text-slate-600 dark:text-slate-300">Tahun</h3>
            <nav class="flex flex-wrap gap-2" aria-label="Filter berdasarkan tahun">
                <a href="{{ route('monitoring.index', $yearFilterQuery) }}"
                    @class([
                        'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                        'border-slate-800 bg-slate-800 text-white dark:border-slate-100 dark:bg-slate-100 dark:text-slate-900' => $selectedYear === null,
                        'border-slate-200 bg-white text-slate-600 hover:border-slate-400 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' => $selectedYear !== null,
                    ])
                    @if ($selectedYear === null) aria-current="page" @endif>
                    Semua
                </a>
                @foreach ($years as $year)
                    <a href="{{ route('monitoring.index', array_merge($yearFilterQuery, ['tahun' => $year])) }}"
                        @class([
                            'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                            'border-slate-800 bg-slate-800 text-white dark:border-slate-100 dark:bg-slate-100 dark:text-slate-900' => $selectedYear === (int) $year,
                            'border-slate-200 bg-white text-slate-600 hover:border-slate-400 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' => $selectedYear !== (int) $year,
                        ])
                        @if ($selectedYear === (int) $year) aria-current="page" @endif>
                        {{ $year }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <h3 class="w-24 shrink-0 text-sm font-semibold text-slate-600 dark:text-slate-300">Triwulan</h3>
            <nav class="flex flex-wrap gap-2" aria-label="Filter berdasarkan triwulan">
                <a href="{{ route('monitoring.index', $quarterFilterQuery) }}"
                    @class([
                        'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                        'border-slate-800 bg-slate-800 text-white dark:border-slate-100 dark:bg-slate-100 dark:text-slate-900' => $selectedQuarter === null,
                        'border-slate-200 bg-white text-slate-600 hover:border-slate-400 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' => $selectedQuarter !== null,
                    ])
                    @if ($selectedQuarter === null) aria-current="page" @endif>
                    Semua TW
                </a>
                @foreach ($quarterLabels as $quarter => $label)
                    <a href="{{ route('monitoring.index', array_merge($quarterFilterQuery, ['triwulan' => $quarter])) }}"
                        @class([
                            'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                            'border-slate-800 bg-slate-800 text-white dark:border-slate-100 dark:bg-slate-100 dark:text-slate-900' => $selectedQuarter === $quarter,
                            'border-slate-200 bg-white text-slate-600 hover:border-slate-400 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800' => $selectedQuarter !== $quarter,
                        ])
                        @if ($selectedQuarter === $quarter) aria-current="page" @endif>
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div tabindex="0" role="region" aria-labelledby="monitoring-table-caption"
            class="max-w-full overflow-x-auto focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600">
            <table class="w-full min-w-[850px] divide-y divide-slate-200 text-left dark:divide-slate-700">
                <caption id="monitoring-table-caption" class="sr-only">Daftar monitoring capaian kinerja</caption>
                <thead class="border-b border-slate-200 text-xs font-bold uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="w-12 px-4 py-3.5">No</th>
                        <th scope="col" class="px-4 py-3.5">Judul</th>
                        <th scope="col" class="w-20 px-4 py-3.5">Tahun</th>
                        <th scope="col" class="px-4 py-3.5">Triwulan - Kategori</th>
                        <th scope="col" class="w-32 px-4 py-3.5">Jenis</th>
                        <th scope="col" class="w-32 px-4 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($monitoring as $index => $item)
                        <tr class="text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/60">
                            <td class="w-12 px-4 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                            <th scope="row" class="px-4 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                {{ $item->judul }}
                            </th>
                            <td class="w-20 px-4 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $item->tahun ?? '—' }}</td>
                            <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400">
                                {{ $item->triwulan ?? '—' }} · {{ $item->kategori ?: '—' }}
                            </td>
                            <td class="w-32 px-4 py-4 text-xs text-slate-500 dark:text-slate-400">
                                @if ($item->jenis === 'File' && $item->filesize)
                                    File · {{ $item->filesize }}
                                @else
                                    {{ $item->jenis }}
                                @endif
                            </td>
                            <td class="w-32 px-4 py-4 text-right">
                                @if ($item->document_url && $item->jenis === 'File')
                                    <a href="{{ $item->document_url }}" download
                                        class="inline-flex items-center rounded-md bg-slate-800 px-4 py-1.5 text-xs font-medium text-white transition-colors hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                                        Download
                                    </a>
                                @elseif ($item->document_url)
                                    <a href="{{ $item->document_url }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center rounded-md bg-slate-800 px-4 py-1.5 text-xs font-medium text-white transition-colors hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                                        Buka
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                Belum ada data monitoring
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section aria-labelledby="ikss-monitoring-title"
        class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
            <h3 id="ikss-monitoring-title" class="font-bold text-slate-900 dark:text-slate-100">Daftar IKSS</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tabel read-only berdasarkan periode monitoring terpilih.</p>
        </div>
        <div tabindex="0" role="region" aria-labelledby="ikss-monitoring-caption"
            class="max-w-full overflow-x-auto focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600">
            <p class="px-5 py-2 text-xs text-slate-500 sm:hidden dark:text-slate-400">Geser tabel ke samping untuk melihat seluruh kolom.</p>
            <table class="w-full min-w-[1000px] divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                <caption id="ikss-monitoring-caption" class="sr-only">
                    Tabel IKSS untuk {{ $selectedYear ?? 'semua tahun' }}, {{ $selectedQuarter ?? 'semua triwulan' }}
                </caption>
                <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-900 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="w-16 border-b border-slate-200 px-4 py-4 dark:border-slate-700">No</th>
                        <th scope="col" class="min-w-72 border-b border-slate-200 px-4 py-4 dark:border-slate-700">Sasaran Kegiatan</th>
                        <th scope="col" class="min-w-80 border-b border-slate-200 px-4 py-4 dark:border-slate-700">Indikator Kinerja</th>
                        <th scope="col" class="w-60 border-b border-slate-200 px-4 py-4 text-center dark:border-slate-700">Link Dokumen Sumber</th>
                        <th scope="col" class="w-72 border-b border-slate-200 px-4 py-4 text-center dark:border-slate-700">Link Bukti Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @if ($ikssMasters->isEmpty())
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                Belum ada data IKSS.
                            </td>
                        </tr>
                    @else
                        @foreach ($ikssMasters as $index => $ikssMaster)
                            @php
                                $capaian = $capaianByIkss->get($ikssMaster->id);
                            @endphp
                            <tr class="align-middle text-slate-700 transition-colors hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/60">
                                <td class="whitespace-nowrap px-4 py-5 text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-5 leading-6">{{ $ikssMaster->sasaran_kegiatan }}</td>
                                <th scope="row" class="px-4 py-5 leading-6 font-medium text-slate-800 dark:text-slate-100">
                                    {{ $ikssMaster->indikator_kinerja }}
                                </th>
                                <td class="px-4 py-5 text-center">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Belum tersedia</span>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    @if ($capaian?->evidence_url)
                                        <a href="{{ $capaian->evidence_url }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex min-h-9 items-center rounded-md border border-slate-300 px-3 py-1 text-xs font-medium text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                            Buka bukti
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Belum tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </section>
@endsection
