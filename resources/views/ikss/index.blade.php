@extends('layouts.app')

@section('title', 'Master Data IKSS')

@section('content')
    @php
        $completeCount = (int) $statusCounts->get('Sudah Diisi', 0);
        $partialCount = (int) $statusCounts->get('Sebagian', 0);
        $emptyCount = max(0, $totalCount - $completeCount - $partialCount);
        $completionPercentage = $totalCount > 0 ? round(($completeCount / $totalCount) * 100) : 0;
    @endphp

    <div class="space-y-5">
        @if (session('success'))
            <div role="status" class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200">
                <svg class="mt-0.5 size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" />
                </svg>
                <p>{{ session('success') }}</p>
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

        <header class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center gap-4">
                <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3v18h18M7 14l4-4 4 4 6-7" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Master Data IKSS</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola indikator kinerja dan pantau data capaian.</p>
                </div>
            </div>

            <div x-data>
                <button type="button" @click="$refs.createIkssDialog.showModal()"
                    class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14" />
                    </svg>
                    Tambah IKSS
                </button>

                <dialog x-ref="createIkssDialog" aria-labelledby="create-ikss-title"
                    @if ($errors->any() && old('_dialog') === 'ikss-create') x-init="$nextTick(() => $refs.createIkssDialog.showModal())" @endif
                    class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                    <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-900 sm:px-7">
                        <div>
                            <h2 id="create-ikss-title" class="text-lg font-bold text-slate-900 dark:text-slate-100">Tambah Indikator IKSS</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Masukkan informasi indikator sasaran strategis.</p>
                        </div>
                        <button type="button" @click="$refs.createIkssDialog.close()" aria-label="Tutup dialog"
                            class="grid size-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:hover:bg-slate-800 dark:hover:text-white">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('ikss.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-5 px-5 py-5 sm:px-7">
                            @include('ikss.partials.ikss-fields', [
                                'ikssMaster' => $newIkssMaster,
                                'formId' => 'ikss-create',
                                'dialogKey' => 'ikss-create',
                            ])
                        </div>
                        <div class="sticky bottom-0 flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-950 sm:px-7">
                            <button type="button" @click="$refs.createIkssDialog.close()"
                                class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                Batal
                            </button>
                            <button type="submit"
                                class="min-h-11 rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                                Simpan IKSS
                            </button>
                        </div>
                    </form>
                </dialog>
            </div>
        </header>

        <section aria-label="Ringkasan capaian" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Total indikator</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalCount }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">IKSS terdaftar</p>
            </article>
            <article class="rounded-xl border border-emerald-200 bg-white p-4 shadow-sm dark:border-emerald-900 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Sudah diisi</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $completeCount }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Lengkap untuk periode ini</p>
            </article>
            <article class="rounded-xl border border-amber-200 bg-white p-4 shadow-sm dark:border-amber-900 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Sebagian</p>
                <p class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $partialCount }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Masih perlu dilengkapi</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Belum diisi</p>
                <p class="mt-2 text-2xl font-bold text-slate-700 dark:text-slate-200">{{ $emptyCount }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Belum ada data capaian</p>
            </article>
        </section>

        <section aria-labelledby="ikss-table-title" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <form action="{{ route('ikss.index') }}" method="GET">
                <div class="flex flex-wrap items-center justify-between gap-5 border-b border-slate-200 px-5 py-5 dark:border-slate-700 sm:px-6">
                    <div>
                        <h3 id="ikss-table-title" class="font-bold text-slate-900 dark:text-slate-100">Tabel IKSS</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Indikator Kinerja Sasaran Strategis</p>
                    </div>
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="grid gap-1">
                            <label for="tahun" class="text-xs font-medium text-slate-500 dark:text-slate-400">Tahun</label>
                            <select id="tahun" name="tahun"
                                class="min-h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" @selected($tahun === (int) $year)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <fieldset class="grid gap-1">
                            <legend class="text-xs font-medium text-slate-500 dark:text-slate-400">Triwulan</legend>
                            <div class="flex flex-wrap gap-2">
                                @foreach (\App\Models\Periode::QUARTERS as $quarter)
                                    <label @class([
                                        'inline-flex min-h-10 cursor-pointer items-center rounded-lg border px-3 text-sm font-semibold transition focus-within:ring-2 focus-within:ring-blue-600',
                                        'border-blue-700 bg-blue-700 text-white' => $triwulan === $quarter,
                                        'border-slate-300 bg-white text-slate-600 hover:border-blue-300 hover:bg-blue-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700' => $triwulan !== $quarter,
                                    ])>
                                        <input class="sr-only" type="radio" name="triwulan" value="{{ $quarter }}" @checked($triwulan === $quarter)>
                                        {{ str_replace('TW', 'TW ', $quarter) }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 px-5 py-3 dark:bg-slate-950 sm:px-6">
                    <label for="per_page" class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        Tampilkan
                        <select id="per_page" name="per_page"
                            class="min-h-9 rounded-md border border-slate-300 bg-white px-2 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                            @foreach ([5, 10, 25, 50] as $pageSize)
                                <option value="{{ $pageSize }}" @selected($perPage === $pageSize)>{{ $pageSize }}</option>
                            @endforeach
                        </select>
                        per halaman
                    </label>
                    <button type="submit"
                        class="inline-flex min-h-9 items-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                        Tampilkan
                    </button>
                </div>
            </form>

            <div tabindex="0" role="region" aria-labelledby="ikss-caption"
                class="max-w-full overflow-x-auto focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600">
                <p class="px-5 py-2 text-xs text-slate-500 sm:hidden dark:text-slate-400">Geser tabel ke samping untuk melihat seluruh kolom.</p>
                <table class="w-full min-w-[1000px] divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                    <caption id="ikss-caption" class="sr-only">Tabel IKSS tahun {{ $tahun }}, {{ $triwulan }}</caption>
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <tr>
                            <th scope="col" class="w-28 px-4 py-4">No</th>
                            <th scope="col" class="min-w-72 px-4 py-4">Sasaran Kegiatan</th>
                            <th scope="col" class="min-w-80 px-4 py-4">Indikator Kinerja</th>
                            <th scope="col" class="w-60 px-4 py-4 text-center">Link Dokumen Sumber</th>
                            <th scope="col" class="w-72 px-4 py-4 text-center">Link Bukti Tindak Lanjut TW Sebelumnya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($ikss as $ikssMaster)
                            <tr class="align-middle text-slate-700 transition-colors hover:bg-blue-50/50 dark:text-slate-200 dark:hover:bg-slate-800/60">
                                <th scope="row" class="whitespace-nowrap px-4 py-5 font-semibold text-blue-700 dark:text-blue-300">
                                    {{ $ikssMaster->kode_indikator }}
                                </th>
                                <td class="px-4 py-5 leading-6">{{ $ikssMaster->sasaran_kegiatan }}</td>
                                <td class="px-4 py-5 leading-6">
                                    <p class="font-medium text-slate-800 dark:text-slate-100">{{ $ikssMaster->indikator_kinerja }}</p>
                                    @if ($ikssMaster->target)
                                        <p class="mt-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Target: {{ $ikssMaster->target }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Belum tersedia</span>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Belum tersedia</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-16 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada indikator IKSS.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700 sm:px-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-slate-100">Monitoring Capaian</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            {{ str_replace('TW', 'Triwulan ', $triwulan) }} {{ $tahun }}
                            <span aria-hidden="true">·</span> {{ $ikss->count() }} indikator pada halaman ini
                        </p>
                    </div>
                    <div class="w-full max-w-xs sm:w-56" aria-label="Persentase indikator lengkap">
                        <div class="mb-1.5 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>Capaian lengkap</span>
                            <span>{{ $completeCount }} / {{ $totalCount }}</span>
                        </div>
                        <div role="progressbar" aria-label="IKSS sudah diisi" aria-valuemin="0"
                            aria-valuemax="{{ max(1, $totalCount) }}" aria-valuenow="{{ $completeCount }}"
                            aria-valuetext="{{ $completeCount }} dari {{ $totalCount }} indikator lengkap"
                            class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <span class="block h-full rounded-full bg-emerald-500 transition-[width]" style="width: {{ $completionPercentage }}%"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div tabindex="0" role="region" aria-labelledby="ikss-table-caption"
                class="max-w-full overflow-x-auto focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600">
                <p class="px-5 py-2 text-xs text-slate-500 sm:hidden dark:text-slate-400">
                    Geser tabel ke samping untuk melihat seluruh kolom.
                </p>
                <table class="w-full min-w-[1150px] divide-y divide-slate-200 text-left text-sm dark:divide-slate-700">
                    <caption id="ikss-table-caption" class="sr-only">Monitoring capaian kinerja {{ $triwulan }} {{ $tahun }}</caption>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                        <tr>
                            <th scope="col" class="w-32 px-4 py-3.5">Kode IKSS</th>
                            <th scope="col" class="min-w-72 px-4 py-3.5">Indikator Kinerja</th>
                            <th scope="col" class="w-28 px-4 py-3.5">Target PK</th>
                            <th scope="col" class="w-28 px-4 py-3.5">Target TW</th>
                            <th scope="col" class="w-28 px-4 py-3.5">Realisasi</th>
                            <th scope="col" class="w-28 px-4 py-3.5">Capaian (%)</th>
                            <th scope="col" class="w-36 px-4 py-3.5">TW Sebelumnya</th>
                            <th scope="col" class="w-32 px-4 py-3.5">Status</th>
                            <th scope="col" class="w-80 px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($ikss as $ikssMaster)
                            @php
                                $capaian = $capaianByIkss->get($ikssMaster->id);
                                $status = $capaian?->status ?? 'Kosong';
                            @endphp
                            <tr @class([
                                'align-top text-slate-700 transition-colors hover:bg-blue-50/50 dark:text-slate-200 dark:hover:bg-slate-800/60',
                                'border-l-4 border-l-emerald-500' => $status === 'Sudah Diisi',
                                'border-l-4 border-l-amber-500' => $status === 'Sebagian',
                                'border-l-4 border-l-slate-300 dark:border-l-slate-600' => $status === 'Kosong',
                            ])>
                                <th scope="row" class="whitespace-nowrap px-4 py-4">
                                    <span class="inline-flex rounded-md border border-orange-200 bg-orange-50 px-2.5 py-1 font-semibold text-orange-700 dark:border-orange-900 dark:bg-orange-950/50 dark:text-orange-300">
                                        {{ $ikssMaster->kode_indikator }}
                                    </span>
                                </th>
                                <td class="max-w-96 px-4 py-4 leading-6 font-medium text-slate-800 dark:text-slate-100">{{ $ikssMaster->indikator_kinerja }}</td>
                                <td class="px-4 py-4">{{ $capaian?->target_pk ?? '—' }}</td>
                                <td class="px-4 py-4">{{ $capaian?->target_tw ?? '—' }}</td>
                                <td class="px-4 py-4">{{ $capaian?->realisasi ?? '—' }}</td>
                                <td class="px-4 py-4">{{ $capaian?->capaian_persen ?? '—' }}</td>
                                <td class="px-4 py-4">{{ $capaian?->capaian_tw_sebelumnya ?? '—' }}</td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' => $status === 'Sudah Diisi',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200' => $status === 'Sebagian',
                                        'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' => $status === 'Kosong',
                                    ])>
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                                    <div x-data class="flex flex-wrap justify-end gap-2">
                                        <button type="button" aria-label="Isi atau edit capaian {{ $ikssMaster->kode_indikator }}" @click="$refs.capaianDialog.showModal()"
                                            class="inline-flex min-h-10 items-center rounded-lg bg-orange-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                                            {{ $capaian ? 'Edit Capaian' : 'Isi Capaian' }}
                                        </button>
                                        <button type="button" aria-label="Edit IKSS {{ $ikssMaster->kode_indikator }}" @click="$refs.editIkssDialog.showModal()"
                                            class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                            Edit IKSS
                                        </button>
                                        @if ($capaian)
                                            <button type="button" aria-label="Hapus capaian {{ $ikssMaster->kode_indikator }}" @click="$refs.deleteCapaianDialog.showModal()"
                                                class="inline-flex min-h-10 items-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950">
                                                Hapus Capaian
                                            </button>
                                        @endif
                                        @if ($ikssMaster->capaian_kinerjas_count === 0)
                                            <button type="button" aria-label="Hapus IKSS {{ $ikssMaster->kode_indikator }}" @click="$refs.deleteIkssDialog.showModal()"
                                                class="inline-flex min-h-10 items-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950">
                                                Hapus IKSS
                                            </button>
                                        @else
                                            <span class="max-w-32 self-center text-right text-xs text-slate-500 dark:text-slate-400">
                                                Hapus dikunci: ada riwayat capaian
                                            </span>
                                        @endif
                                    <dialog x-ref="capaianDialog" aria-labelledby="capaian-dialog-title-{{ $ikssMaster->id }}"
                                        @if ($errors->any() && old('_dialog') === 'capaian-'.$ikssMaster->id) x-init="$nextTick(() => $refs.capaianDialog.showModal())" @endif
                                        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-900 sm:px-7">
                                            <div>
                                                <h2 id="capaian-dialog-title-{{ $ikssMaster->id }}" class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                                    {{ $capaian ? 'Edit Capaian Kinerja' : 'Input Capaian Kinerja' }}
                                                </h2>
                                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                    <span class="font-semibold text-orange-700 dark:text-orange-300">{{ $ikssMaster->kode_indikator }}</span>
                                                    <span aria-hidden="true">·</span>
                                                    {{ str_replace('TW', 'Triwulan ', $triwulan) }} {{ $tahun }}
                                                </p>
                                            </div>
                                            <button type="button" @click="$refs.capaianDialog.close()" aria-label="Tutup dialog"
                                                class="grid size-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:hover:bg-slate-800 dark:hover:text-white">
                                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                                                </svg>
                                            </button>
                                        </div>
                                        <form action="{{ route('ikss.capaian.update', $ikssMaster) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid gap-5 px-5 py-5 sm:px-7">
                                                <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $ikssMaster->indikator_kinerja }}</p>
                                                @include('ikss.partials.capaian-fields', [
                                                    'formId' => 'capaian-'.$ikssMaster->id,
                                                    'dialogKey' => 'capaian-'.$ikssMaster->id,
                                                ])
                                            </div>
                                            <div class="sticky bottom-0 flex justify-between gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-950 sm:px-7">
                                                @if ($capaian)
                                                    <button type="button" @click="$refs.deleteCapaianDialog.showModal()"
                                                        class="min-h-11 rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950">
                                                        Hapus Capaian
                                                    </button>
                                                @else
                                                    <span></span>
                                                @endif
                                                <div class="flex gap-3">
                                                    <button type="button" @click="$refs.capaianDialog.close()"
                                                        class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="min-h-11 rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                                                        Simpan Capaian
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </dialog>

                                    <dialog x-ref="editIkssDialog" aria-labelledby="edit-ikss-title-{{ $ikssMaster->id }}"
                                        @if ($errors->any() && old('_dialog') === 'ikss-update-'.$ikssMaster->id) x-init="$nextTick(() => $refs.editIkssDialog.showModal())" @endif
                                        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                        <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-900 sm:px-7">
                                            <div>
                                                <h2 id="edit-ikss-title-{{ $ikssMaster->id }}" class="text-lg font-bold text-slate-900 dark:text-slate-100">Edit Indikator IKSS</h2>
                                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perbarui informasi indikator sasaran strategis.</p>
                                            </div>
                                            <button type="button" @click="$refs.editIkssDialog.close()" aria-label="Tutup dialog"
                                                class="grid size-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:hover:bg-slate-800 dark:hover:text-white">
                                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                                                </svg>
                                            </button>
                                        </div>
                                        <form action="{{ route('ikss.update', $ikssMaster) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid gap-5 px-5 py-5 sm:px-7">
                                                @include('ikss.partials.ikss-fields', [
                                                    'ikssMaster' => $ikssMaster,
                                                    'formId' => 'ikss-update-'.$ikssMaster->id,
                                                    'dialogKey' => 'ikss-update-'.$ikssMaster->id,
                                                ])
                                            </div>
                                            <div class="sticky bottom-0 flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-700 dark:bg-slate-950 sm:px-7">
                                                <button type="button" @click="$refs.editIkssDialog.close()"
                                                    class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                    class="min-h-11 rounded-lg bg-orange-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </dialog>

                                    @if ($capaian)
                                        <dialog x-ref="deleteCapaianDialog" aria-labelledby="delete-capaian-title-{{ $ikssMaster->id }}"
                                            class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-slate-200 bg-white p-6 text-slate-800 shadow-2xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                            <h2 id="delete-capaian-title-{{ $ikssMaster->id }}" class="text-lg font-bold">Hapus data capaian?</h2>
                                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                                Data capaian {{ $ikssMaster->kode_indikator }} untuk {{ $triwulan }} {{ $tahun }} akan dihapus.
                                            </p>
                                            <div class="mt-6 flex justify-end gap-3">
                                                <button type="button" autofocus @click="$refs.deleteCapaianDialog.close()"
                                                    class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-600">
                                                    Batal
                                                </button>
                                                <form action="{{ route('capaian-kinerja.destroy', $capaian) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="min-h-11 rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800">
                                                        Hapus Capaian
                                                    </button>
                                                </form>
                                            </div>
                                        </dialog>
                                    @elseif ($ikssMaster->capaian_kinerjas_count === 0)
                                        <dialog x-ref="deleteIkssDialog" aria-labelledby="delete-ikss-title-{{ $ikssMaster->id }}"
                                            class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-slate-200 bg-white p-6 text-slate-800 shadow-2xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                            <h2 id="delete-ikss-title-{{ $ikssMaster->id }}" class="text-lg font-bold">Hapus IKSS?</h2>
                                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                                Indikator {{ $ikssMaster->kode_indikator }} akan dihapus. Data yang memiliki riwayat capaian tidak dapat dihapus.
                                            </p>
                                            <div class="mt-6 flex justify-end gap-3">
                                                <button type="button" autofocus @click="$refs.deleteIkssDialog.close()"
                                                    class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold dark:border-slate-600">
                                                    Batal
                                                </button>
                                                <form action="{{ route('ikss.destroy', $ikssMaster) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                                                    <input type="hidden" name="triwulan" value="{{ $triwulan }}">
                                                    <button type="submit"
                                                        class="min-h-11 rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-800">
                                                        Hapus IKSS
                                                    </button>
                                                </form>
                                            </div>
                                        </dialog>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-16 text-center">
                                    <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                                        <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                                        </svg>
                                    </span>
                                    <h4 class="mt-4 font-semibold text-slate-900 dark:text-slate-100">Belum ada indikator IKSS</h4>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tambahkan indikator untuk mulai mencatat capaian kinerja.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-200 bg-slate-50 px-5 py-3 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">
                <div>
                    <p>Menampilkan {{ $ikss->firstItem() ?? 0 }}–{{ $ikss->lastItem() ?? 0 }} dari {{ $totalCount }} data</p>
                    <p class="mt-1 text-xs">{{ $completeCount }} lengkap <span aria-hidden="true">·</span> {{ $partialCount }} sebagian <span aria-hidden="true">·</span> {{ $emptyCount }} belum diisi</p>
                </div>
                @if ($ikss->hasPages())
                    @php
                        $paginationStart = max(1, $ikss->currentPage() - 2);
                        $paginationEnd = min($ikss->lastPage(), $ikss->currentPage() + 2);
                    @endphp
                    <nav class="flex items-center gap-1" aria-label="Navigasi halaman tabel">
                        @if ($ikss->onFirstPage())
                            <span aria-disabled="true" class="grid size-9 place-items-center rounded-md border border-slate-200 text-slate-300 dark:border-slate-700 dark:text-slate-600">‹</span>
                        @else
                            <a href="{{ $ikss->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"
                                class="grid size-9 place-items-center rounded-md border border-slate-300 text-slate-600 hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">‹</a>
                        @endif
                        @for ($page = $paginationStart; $page <= $paginationEnd; $page++)
                            @if ($page === $ikss->currentPage())
                                <span aria-current="page" class="grid size-9 place-items-center rounded-md bg-blue-600 font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $ikss->url($page) }}" aria-label="Halaman {{ $page }}"
                                    class="grid size-9 place-items-center rounded-md border border-slate-300 text-slate-600 hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">{{ $page }}</a>
                            @endif
                        @endfor
                        @if ($ikss->hasMorePages())
                            <a href="{{ $ikss->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"
                                class="grid size-9 place-items-center rounded-md border border-slate-300 text-slate-600 hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">›</a>
                        @else
                            <span aria-disabled="true" class="grid size-9 place-items-center rounded-md border border-slate-200 text-slate-300 dark:border-slate-700 dark:text-slate-600">›</span>
                        @endif
                    </nav>
                @endif
            </div>
        </section>
    </div>
@endsection
