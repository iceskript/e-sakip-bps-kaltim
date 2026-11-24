@extends('layouts.app')

@section('title', 'Entry Capaian Kinerja Triwulanan')

@section('content')
    <div class="space-y-5">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-200">
                <p class="font-semibold">Periksa kembali data yang dimasukkan.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Entry Capaian Kinerja Triwulanan</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Input data capaian per IKU per triwulan — BPS Kaltim</p>
                </div>
            </div>

            <div x-data>
                <button type="button" @click="$refs.createEntryIkssDialog.showModal()"
                    class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14" />
                    </svg>
                    Tambah Indikator
                </button>

                <dialog x-ref="createEntryIkssDialog" aria-labelledby="create-entry-ikss-title"
                    @if ($errors->any() && old('_dialog') === 'entry-ikss-create') x-init="$nextTick(() => $refs.createEntryIkssDialog.showModal())" @endif
                    class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                    <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-900 sm:px-7">
                        <div>
                            <h2 id="create-entry-ikss-title" class="text-lg font-bold text-slate-900 dark:text-slate-100">Tambah Indikator Kinerja</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Indikator tersimpan sebagai data master dan tersedia untuk semua tahun/triwulan.</p>
                        </div>
                        <button type="button" @click="$refs.createEntryIkssDialog.close()" aria-label="Tutup dialog"
                            class="grid size-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:hover:bg-slate-800 dark:hover:text-white">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('ikss.store') }}" method="POST" aria-labelledby="create-entry-ikss-title">
                        @csrf
                        <input type="hidden" name="return_to" value="entry">
                        <div class="grid gap-5 px-5 py-5 sm:px-7">
                            @include('ikss.partials.ikss-fields', [
                                'ikssMaster' => $newIkssMaster,
                                'formId' => 'entry-ikss-create',
                                'dialogKey' => 'entry-ikss-create',
                            ])
                        </div>
                        <div class="sticky bottom-0 flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-950 sm:px-7">
                            <button type="button" @click="$refs.createEntryIkssDialog.close()"
                                class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                Batal
                            </button>
                            <button type="submit"
                                class="min-h-11 rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                                Simpan Indikator
                            </button>
                        </div>
                    </form>
                </dialog>
            </div>
        </header>

        <section aria-labelledby="entry-progress-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/50 dark:text-orange-300">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3v18h18M7 14l4-4 4 3 5-7" />
                        </svg>
                    </span>
                    <div>
                        <h3 id="entry-progress-heading" class="text-sm font-bold text-slate-800 dark:text-slate-100">Capaian Kinerja Triwulanan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ringkasan pengisian indikator kinerja</p>
                    </div>
                </div>
                <span class="rounded-full bg-orange-50 px-3 py-1.5 text-xs font-semibold text-orange-700 dark:bg-orange-950/50 dark:text-orange-300">
                    {{ $triwulanLabel }} — {{ $tahun }}
                </span>
            </div>

            <div class="grid gap-5 p-5 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.8fr)] lg:items-end">
                <search>
                    <form action="{{ route('capaian-kinerja.index') }}" method="GET" aria-label="Filter tahun dan triwulan" class="flex flex-wrap items-end gap-3">
                        <div class="grid min-w-36 gap-1.5">
                            <label for="tahun-filter" class="text-xs font-semibold text-slate-600 dark:text-slate-300">Tahun</label>
                            <select id="tahun-filter" name="tahun" class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" @selected($tahun === $year)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid min-w-40 gap-1.5">
                            <label for="triwulan-filter" class="text-xs font-semibold text-slate-600 dark:text-slate-300">Triwulan</label>
                            <select id="triwulan-filter" name="triwulan" class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-500 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100">
                                @foreach (['TW1' => 'TW I', 'TW2' => 'TW II', 'TW3' => 'TW III', 'TW4' => 'TW IV'] as $value => $label)
                                    <option value="{{ $value }}" @selected($triwulan === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="min-h-11 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                            Tampilkan
                        </button>
                    </form>
                </search>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Progres pengisian</p>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $completedIndikators }}</span>
                            dari {{ $totalIndikators }} IKU telah diisi
                        </p>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800" role="progressbar" aria-label="Progres pengisian IKU" aria-valuetext="{{ $completedIndikators }} dari {{ $totalIndikators }} IKU telah diisi" aria-valuemin="0" aria-valuemax="{{ max($totalIndikators, 1) }}" aria-valuenow="{{ $completedIndikators }}">
                        <div class="h-full rounded-full bg-orange-500 transition-all" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </section>

        @forelse ($indikators as $groupTitle => $items)
            <section aria-labelledby="group-title-{{ $loop->index }}" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <h3 id="group-title-{{ $loop->index }}" class="border-b border-slate-100 px-5 py-3 text-xs font-bold uppercase tracking-wide text-slate-600 dark:border-slate-800 dark:text-slate-300">
                    {{ $groupTitle }}
                </h3>

                <div tabindex="0" role="region" aria-labelledby="group-title-{{ $loop->index }}" class="max-w-full overflow-x-auto focus-visible:outline focus-visible:outline-2 focus-visible:outline-orange-500">
                    <table class="w-full min-w-[850px] text-left">
                        <caption class="sr-only">Entry capaian indikator {{ $groupTitle }} tahun {{ $tahun }}, {{ $triwulanLabel }}</caption>
                        <thead class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                            <tr class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                                <th scope="col" class="w-24 px-5 py-3">Kode</th>
                                <th scope="col" class="px-5 py-3">Indikator Kinerja</th>
                                <th scope="col" class="w-36 px-5 py-3">Status</th>
                                <th scope="col" class="w-52 px-5 py-3">Terakhir Diubah</th>
                                <th scope="col" class="w-44 px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($items as $ikss)
                                @php
                                    $capaian = $ikss->capaianKinerjas->first();
                                    $status = $capaian?->status === 'Sudah Diisi' ? 'Sudah Diisi' : 'Sebagian';
                                    $statusClasses = $status === 'Sudah Diisi'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-300'
                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-300';
                                    $dialogId = 'capaian-entry-dialog-'.$ikss->id;
                                    $isEntryErrorDialog = $errors->any() && (string) old('_dialog') === (string) $ikss->id;
                                    $firstInvalidEntryField = $isEntryErrorDialog
                                        ? ($errors->has('realisasi') ? 'realisasi' : ($errors->has('link_bukti') ? 'link_bukti' : ($errors->has('kendala') ? 'kendala' : 'realisasi')))
                                        : 'realisasi';
                                @endphp
                                <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                    <td class="border-l-2 border-orange-400 px-5 py-4 align-top">
                                        <span class="inline-flex rounded bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                            {{ $ikss->kode_indikator }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $ikss->indikator_kinerja }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $ikss->sasaran_kegiatan }}</p>
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $status }}</span>
                                    </td>
                                    <td class="px-5 py-4 align-top text-xs text-slate-500 dark:text-slate-400">
                                        @if ($capaian)
                                            <span class="block">{{ $capaian->updatedBy?->name ?? $capaian->updatedBy?->username ?? 'Administrator' }}</span>
                                            <time class="mt-1 block" datetime="{{ $capaian->updated_at?->toIso8601String() }}">
                                                {{ $capaian->updated_at?->format('Y-m-d H:i') ?? '—' }}
                                            </time>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center align-top">
                                        <div x-data class="flex flex-col items-center gap-2 sm:flex-row sm:justify-center">
                                            <button type="button" @click="$refs.entryDialog.showModal()" aria-label="Edit capaian {{ $ikss->kode_indikator }}"
                                                class="min-h-9 rounded-md bg-orange-500 px-4 py-1.5 text-xs font-medium text-white transition hover:bg-orange-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
                                                Edit
                                            </button>
                                            @if ($ikss->capaian_kinerjas_exists)
                                                <span class="max-w-32 text-xs text-slate-500 dark:text-slate-400">
                                                    Hapus dikunci: ada riwayat capaian
                                                </span>
                                            @else
                                                <button type="button" @click="$refs.deleteEntryDialog.showModal()" aria-label="Hapus indikator {{ $ikss->kode_indikator }}"
                                                    class="min-h-9 rounded-md border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950">
                                                    Hapus
                                                </button>

                                                <dialog x-ref="deleteEntryDialog" aria-labelledby="delete-entry-dialog-title-{{ $ikss->id }}"
                                                    aria-describedby="delete-entry-dialog-description-{{ $ikss->id }}"
                                                    class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-slate-200 bg-white p-6 text-left text-slate-800 shadow-2xl backdrop:bg-slate-950/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                                    <h2 id="delete-entry-dialog-title-{{ $ikss->id }}" class="text-lg font-bold">Hapus indikator?</h2>
                                                    <p id="delete-entry-dialog-description-{{ $ikss->id }}" class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                                        Indikator {{ $ikss->kode_indikator }} — {{ $ikss->indikator_kinerja }} akan dihapus permanen.
                                                    </p>
                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <button type="button" autofocus @click="$refs.deleteEntryDialog.close()"
                                                            class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-600 dark:text-slate-200">
                                                            Batal
                                                        </button>
                                                        <form action="{{ route('ikss.destroy', $ikss) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                                                            <input type="hidden" name="triwulan" value="{{ $triwulan }}">
                                                            <input type="hidden" name="return_to" value="entry">
                                                            <button type="submit"
                                                                class="min-h-11 rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800">
                                                                Ya, hapus indikator
                                                            </button>
                                                        </form>
                                                    </div>
                                                </dialog>
                                            @endif

                                            <dialog id="{{ $dialogId }}" x-ref="entryDialog" aria-labelledby="{{ $dialogId }}-title"
                                                @if ($isEntryErrorDialog) x-init="$nextTick(() => $refs.entryDialog.showModal())" @endif
                                                class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-slate-950/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-7">
                                                    <div>
                                                        <h1 id="{{ $dialogId }}-title" class="text-lg font-bold text-slate-900 dark:text-slate-100">Edit Capaian: {{ $ikss->indikator_kinerja }}</h1>
                                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $ikss->kode_indikator }} · {{ $tahun }} · {{ $triwulanLabel }}</p>
                                                    </div>
                                                    <button type="button" @click="$refs.entryDialog.close()" aria-label="Tutup dialog"
                                                        class="grid size-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:hover:bg-slate-800 dark:hover:text-white">
                                                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <form action="{{ route('capaian-kinerja.update', $ikss) }}" method="POST" aria-labelledby="{{ $dialogId }}-title">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                                    <input type="hidden" name="triwulan" value="{{ $triwulan }}">
                                                    <input type="hidden" name="_dialog" value="{{ $ikss->id }}">

                                                    <div class="grid gap-4 px-5 py-5 sm:px-7">
                                                        <div class="grid gap-1.5">
                                                            <label for="target-{{ $ikss->id }}" class="text-sm font-medium text-slate-700 dark:text-slate-200">Target</label>
                                                            <input id="target-{{ $ikss->id }}" type="text" value="{{ $ikss->target }}" readonly
                                                                class="min-h-11 rounded-lg border border-slate-200 bg-slate-100 px-3 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                        </div>
                                                        <div class="grid gap-1.5">
                                                            <label for="realisasi-{{ $ikss->id }}" class="text-sm font-medium text-slate-700 dark:text-slate-200">Realisasi <span aria-hidden="true">*</span></label>
                                                            <input id="realisasi-{{ $ikss->id }}" name="realisasi" type="number" min="0" max="99999999.99" step="0.01"
                                                                value="{{ old('realisasi', $capaian?->realisasi) }}" required
                                                                @if ($isEntryErrorDialog && $firstInvalidEntryField === 'realisasi') autofocus @endif
                                                                @if ($errors->has('realisasi') && $isEntryErrorDialog) aria-invalid="true" aria-describedby="realisasi-error-{{ $ikss->id }}" @endif
                                                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100">
                                                            @if ($errors->has('realisasi') && $isEntryErrorDialog)
                                                                <p id="realisasi-error-{{ $ikss->id }}" class="text-xs text-red-600 dark:text-red-400">{{ $errors->first('realisasi') }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="grid gap-1.5">
                                                            <label for="link-bukti-{{ $ikss->id }}" class="text-sm font-medium text-slate-700 dark:text-slate-200">Link Bukti Dukung</label>
                                                            <input id="link-bukti-{{ $ikss->id }}" name="link_bukti" type="url" maxlength="2048"
                                                                value="{{ old('link_bukti', $capaian?->link_bukti) }}"
                                                                @if ($isEntryErrorDialog && $firstInvalidEntryField === 'link_bukti') autofocus @endif
                                                                @if ($errors->has('link_bukti') && $isEntryErrorDialog) aria-invalid="true" aria-describedby="link-bukti-error-{{ $ikss->id }}" @endif
                                                                autocorrect="off" autocapitalize="none" spellcheck="false"
                                                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100">
                                                            @if ($errors->has('link_bukti') && $isEntryErrorDialog)
                                                                <p id="link-bukti-error-{{ $ikss->id }}" class="text-xs text-red-600 dark:text-red-400">{{ $errors->first('link_bukti') }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="grid gap-1.5">
                                                            <label for="kendala-{{ $ikss->id }}" class="text-sm font-medium text-slate-700 dark:text-slate-200">Kendala/Tindak Lanjut</label>
                                                            <textarea id="kendala-{{ $ikss->id }}" name="kendala" rows="4" maxlength="5000"
                                                                @if ($errors->has('kendala') && $isEntryErrorDialog) aria-invalid="true" aria-describedby="kendala-error-{{ $ikss->id }}" @endif
                                                                @if ($isEntryErrorDialog && $firstInvalidEntryField === 'kendala') autofocus @endif
                                                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-950 dark:text-slate-100">{{ old('kendala', $capaian?->kendala) }}</textarea>
                                                            @if ($errors->has('kendala') && $isEntryErrorDialog)
                                                                <p id="kendala-error-{{ $ikss->id }}" class="text-xs text-red-600 dark:text-red-400">{{ $errors->first('kendala') }}</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-950 sm:px-7">
                                                        <button type="button" @click="$refs.entryDialog.close()"
                                                            class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="min-h-11 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </form>
                                            </dialog>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-12 text-center text-sm text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                Belum ada data IKSS.
            </div>
        @endforelse
    </div>
@endsection
