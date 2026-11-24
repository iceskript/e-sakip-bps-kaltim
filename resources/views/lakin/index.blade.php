@extends('layouts.app')

@section('title', 'LAKIN')

@section('content')
    <div x-data="{
        activeTab: @js($selectedType),
        createModalOpen: @js($errors->any() && old('_dialog') === 'lakin-create'),
        focusTab(type) {
            this.activeTab = type;
            this.$nextTick(() => this.$refs[type.toLowerCase()].focus());
        }
    }"
        x-init="if (createModalOpen) $nextTick(() => $refs.lakinDialog.showModal())"
        class="mx-auto max-w-7xl space-y-6">
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <p class="font-semibold">Periksa kembali data LAKIN.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <header class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-slate-100 text-slate-700 dark:bg-gray-800 dark:text-gray-100">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H5V3h2Zm7 0v5h5M8 13h8m-8 4h8" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-gray-100">Laporan Akuntabilitas Kinerja</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-gray-300">Kelola dokumen LAKIN Draft dan Final.</p>
                </div>
            </div>
            <button type="button" @click="$refs.lakinDialog.showModal(); createModalOpen = true"
                class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14" />
                </svg>
                Tambah LAKIN
            </button>
        </header>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-slate-200 px-4 pt-4 dark:border-gray-700 sm:px-6">
                <div class="flex gap-2" role="tablist" aria-label="Jenis laporan LAKIN">
                    @foreach (['Draft', 'Final'] as $type)
                        <button type="button" role="tab"
                            id="lakin-tab-{{ strtolower($type) }}"
                            x-ref="{{ strtolower($type) }}"
                            aria-selected="{{ $selectedType === $type ? 'true' : 'false' }}"
                            :aria-selected="(activeTab === @js($type)).toString()"
                            tabindex="{{ $selectedType === $type ? '0' : '-1' }}"
                            :tabindex="activeTab === @js($type) ? 0 : -1"
                            @click="activeTab = @js($type)"
                            @keydown.right.prevent="focusTab(@js($type === 'Draft' ? 'Final' : 'Draft'))"
                            @keydown.left.prevent="focusTab(@js($type === 'Draft' ? 'Final' : 'Draft'))"
                            @keydown.home.prevent="focusTab('Draft')"
                            @keydown.end.prevent="focusTab('Final')"
                            class="min-h-11 border-b-2 border-transparent px-4 text-sm font-semibold text-slate-500 transition-colors hover:border-slate-300 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 aria-selected:border-blue-700 aria-selected:text-blue-700 dark:text-gray-300 dark:hover:border-gray-500 dark:hover:text-white dark:aria-selected:border-gray-100 dark:aria-selected:text-gray-100">
                            {{ $type }}
                            <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600 dark:bg-gray-700 dark:text-gray-100">
                                {{ $lakinsByType->get($type, collect())->count() }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            @foreach (['Draft', 'Final'] as $type)
                <section x-show="activeTab === @js($type)" x-transition.opacity
                    @if ($selectedType !== $type) hidden @endif
                    :hidden="activeTab !== @js($type)"
                    id="lakin-panel-{{ strtolower($type) }}" role="tabpanel"
                    aria-labelledby="lakin-tab-{{ strtolower($type) }}"
                    class="p-4 sm:p-6">
                    <div tabindex="0" role="region" aria-labelledby="lakin-caption-{{ strtolower($type) }}"
                        class="max-w-full overflow-x-auto rounded-lg border border-slate-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-gray-700">
                        <table class="w-full min-w-[720px] divide-y divide-slate-200 text-left text-sm dark:divide-gray-700">
                            <caption id="lakin-caption-{{ strtolower($type) }}" class="sr-only">Daftar dokumen LAKIN {{ $type }}</caption>
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-gray-900 dark:text-gray-200">
                                <tr>
                                    <th scope="col" class="w-16 px-4 py-3">No</th>
                                    <th scope="col" class="px-4 py-3">Judul</th>
                                    <th scope="col" class="w-24 px-4 py-3">Tahun</th>
                                    <th scope="col" class="w-28 px-4 py-3">Tipe</th>
                                    <th scope="col" class="w-48 px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                                @forelse ($lakinsByType->get($type, collect()) as $lakin)
                                    <tr class="text-slate-700 transition-colors hover:bg-slate-50 dark:text-gray-100 dark:hover:bg-gray-700/60">
                                        <td class="px-4 py-4 text-slate-400 dark:text-gray-400">{{ $loop->iteration }}</td>
                                        <th scope="row" class="px-4 py-4 font-semibold text-slate-900 dark:text-gray-100">
                                            {{ $lakin->judul }}
                                            @if ($lakin->keterangan)
                                                <span class="mt-1 block max-w-2xl whitespace-normal text-xs font-normal text-slate-500 dark:text-gray-300">
                                                    {{ $lakin->keterangan }}
                                                </span>
                                            @endif
                                        </th>
                                        <td class="px-4 py-4">{{ $lakin->tahun }}</td>
                                        <td class="px-4 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-amber-100 text-amber-800 dark:bg-gray-700 dark:text-gray-100' => $lakin->tipe === 'Draft',
                                                'bg-emerald-100 text-emerald-800 dark:bg-gray-700 dark:text-gray-100' => $lakin->tipe === 'Final',
                                            ])>
                                                {{ $lakin->tipe }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap justify-end gap-2">
                                                @if ($lakin->file_path)
                                                    <a href="{{ route('lakin.download', $lakin) }}"
                                                        class="inline-flex min-h-9 items-center rounded-lg bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:bg-gray-700 dark:hover:bg-gray-600">
                                                        Download
                                                    </a>
                                                @endif
                                                @if ($lakin->link_dokumen)
                                                    <a href="{{ $lakin->link_dokumen }}" target="_blank" rel="noopener noreferrer"
                                                        class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:border-gray-600 dark:text-gray-100 dark:hover:bg-gray-700">
                                                        Buka
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('lakin.destroy', $lakin) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="tipe" value="{{ $type }}">
                                                    <button type="submit"
                                                        class="inline-flex min-h-9 items-center rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-700 dark:border-gray-600 dark:text-gray-100 dark:hover:bg-gray-700">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-slate-500 dark:text-gray-300">
                                            Belum ada dokumen LAKIN {{ $type }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endforeach
        </section>

        <dialog x-ref="lakinDialog" @close="createModalOpen = false"
            aria-labelledby="lakin-dialog-title"
            class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-gray-950/60 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <section x-show="createModalOpen" x-transition.opacity>
                <header class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-gray-700 sm:px-7">
                    <div>
                        <h1 id="lakin-dialog-title" class="text-lg font-bold text-slate-900 dark:text-gray-100">Tambah Dokumen LAKIN</h1>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-300">Unggah berkas PDF/Word atau masukkan tautan dokumen.</p>
                    </div>
                    <button type="button" @click="createModalOpen = false; $refs.lakinDialog.close()"
                        class="grid size-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
                        aria-label="Tutup dialog">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                        </svg>
                    </button>
                </header>

                <form action="{{ route('lakin.store') }}" method="POST" enctype="multipart/form-data" aria-labelledby="lakin-dialog-title">
                    @csrf
                    <input type="hidden" name="_dialog" value="lakin-create">

                    <div class="grid gap-4 px-5 py-5 sm:px-7">
                        <div class="grid gap-1.5">
                            <label for="lakin-judul" class="text-sm font-medium text-slate-700 dark:text-gray-200">Judul</label>
                            <input id="lakin-judul" name="judul" type="text" maxlength="255" value="{{ old('judul') }}" required autofocus
                                @if ($errors->has('judul') && old('_dialog') === 'lakin-create') aria-invalid="true" aria-describedby="lakin-judul-error" @endif
                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('judul')
                                <p id="lakin-judul-error" class="text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <label for="lakin-tahun" class="text-sm font-medium text-slate-700 dark:text-gray-200">Tahun</label>
                                <input id="lakin-tahun" name="tahun" type="number" min="2000" max="2100" value="{{ old('tahun', now()->year) }}" required
                                    class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @error('tahun')
                                    <p class="text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <fieldset class="grid gap-1.5">
                                <legend class="text-sm font-medium text-slate-700 dark:text-gray-200">Tipe</legend>
                                <div class="flex min-h-11 items-center gap-4">
                                    @foreach (['Draft', 'Final'] as $type)
                                        <label class="inline-flex min-h-11 items-center gap-2 text-sm text-slate-700 dark:text-gray-200">
                                            <input type="radio" name="tipe" value="{{ $type }}" required
                                                @checked(old('tipe', $selectedType) === $type)
                                                class="size-4 accent-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                                            {{ $type }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('tipe')
                                    <p class="text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>

                        <div class="grid gap-1.5">
                            <label for="lakin-file" class="text-sm font-medium text-slate-700 dark:text-gray-200">Berkas PDF/Word</label>
                            <input id="lakin-file" name="file" type="file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                aria-describedby="lakin-file-help"
                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:file:bg-gray-700 dark:file:text-gray-100">
                            <p id="lakin-file-help" class="text-xs text-slate-500 dark:text-gray-300">Maksimal 20 MB. Isi berkas atau tautan di bawah.</p>
                            @error('file')
                                <p class="text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-1.5">
                            <label for="lakin-link" class="text-sm font-medium text-slate-700 dark:text-gray-200">Tautan dokumen</label>
                            <input id="lakin-link" name="link_dokumen" type="url" maxlength="2048" value="{{ old('link_dokumen') }}"
                                placeholder="https://..."
                                autocorrect="off" autocapitalize="none" spellcheck="false"
                                @if ($errors->has('link_dokumen') && old('_dialog') === 'lakin-create') aria-invalid="true" aria-describedby="lakin-link-error" @endif
                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('link_dokumen')
                                <p id="lakin-link-error" class="text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-1.5">
                            <label for="lakin-keterangan" class="text-sm font-medium text-slate-700 dark:text-gray-200">Keterangan <span class="font-normal text-slate-500">(opsional)</span></label>
                            <textarea id="lakin-keterangan" name="keterangan" rows="3" maxlength="5000"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <footer class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-900 sm:px-7">
                        <button type="button" @click="createModalOpen = false; $refs.lakinDialog.close()"
                            class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-gray-600 dark:text-gray-100 dark:hover:bg-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                            class="min-h-11 rounded-lg bg-blue-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                            Simpan LAKIN
                        </button>
                    </footer>
                </form>
            </section>
        </dialog>
    </div>
@endsection
