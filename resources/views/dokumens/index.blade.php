@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <header class="mb-6 flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
            </svg>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $pageTitle }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
        </div>
    </header>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <nav class="mb-5 flex flex-wrap items-center gap-2" aria-label="Filter dokumen berdasarkan tahun">
            <a href="{{ route('dokumens.index', ['menuKey' => $menuKey]) }}"
                @class([
                    'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                    'border-slate-800 bg-slate-800 text-white dark:border-slate-100 dark:bg-slate-100 dark:text-slate-900' => $selectedYear === null,
                    'border-slate-200 bg-white text-slate-600 hover:border-blue-500 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:text-blue-400' => $selectedYear !== null,
                ])
                @if ($selectedYear === null) aria-current="page" @endif>
                Semua
            </a>

            @foreach ($years as $year)
                <a href="{{ route('dokumens.index', ['menuKey' => $menuKey, 'tahun' => $year]) }}"
                    @class([
                        'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                        'border-slate-800 bg-slate-800 text-white dark:border-slate-100 dark:bg-slate-100 dark:text-slate-900' => $selectedYear === (int) $year,
                        'border-slate-200 bg-white text-slate-600 hover:border-blue-500 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:text-blue-400' => $selectedYear !== (int) $year,
                    ])
                    @if ($selectedYear === (int) $year) aria-current="page" @endif>
                    {{ $year }}
                </a>
            @endforeach
        </nav>

        <div tabindex="0" role="region" aria-labelledby="documents-caption"
            class="max-w-full overflow-x-auto rounded-lg border border-slate-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-slate-700">
            <table class="w-full min-w-[760px] divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                <caption id="documents-caption" class="sr-only">Daftar dokumen {{ $pageTitle }}</caption>
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="w-16 px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3">Judul</th>
                        <th scope="col" class="w-24 px-4 py-3">Tahun</th>
                        <th scope="col" class="px-4 py-3">Kategori</th>
                        <th scope="col" class="w-28 px-4 py-3">Jenis</th>
                        <th scope="col" class="w-28 px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($documents as $document)
                        <tr class="text-slate-700 dark:text-slate-200">
                            <td class="px-4 py-4 text-slate-400">{{ $loop->iteration }}</td>
                            <th scope="row" class="px-4 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                {{ $document->judul }}
                            </th>
                            <td class="px-4 py-4">{{ $document->tahun ?? '—' }}</td>
                            <td class="px-4 py-4">{{ $document->kategori ?: '—' }}</td>
                            <td class="px-4 py-4 text-slate-500 dark:text-slate-400">
                                @if ($document->jenis === 'File' && $document->filesize)
                                    File · {{ $document->filesize }}
                                @else
                                    {{ $document->jenis }}
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                @if ($document->document_url)
                                    <a href="{{ $document->document_url }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-slate-700">
                                        {{ $document->jenis === 'File' ? 'Download' : 'Buka' }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                @if ($selectedYear !== null)
                                    Belum ada dokumen {{ $pageTitle }} untuk tahun {{ $selectedYear }}.
                                @else
                                    Belum ada dokumen {{ $pageTitle }}.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
