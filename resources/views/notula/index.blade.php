@extends('layouts.app')

@section('title', 'Generate Notula Monitoring Kinerja')

@section('content')
    <div x-data="{
        tab: @js($selectedSource),
        selectedFileName: '',
        dragOver: false,
        fileError: '',
        toastMessage: @js(session('success')),
        init() {
            if (this.toastMessage) {
                window.setTimeout(() => this.toastMessage = '', 5000);
            }
        },
        selectTab(tab) {
            this.tab = tab;
            this.$nextTick(() => this.$refs[tab].focus());
        },
        updateFile(event) {
            const file = event.target.files.length ? event.target.files[0] : null;
            this.selectedFileName = file ? file.name : '';
            this.fileError = '';
        },
        handleDrop(event) {
            this.dragOver = false;
            const file = event.dataTransfer.files.length ? event.dataTransfer.files[0] : null;
            if (!file || !file.name.toLowerCase().endsWith('.xlsx')) {
                this.fileError = 'Pilih file Excel dengan ekstensi .xlsx.';
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(file);
            this.$refs.excelInput.files = transfer.files;
            this.selectedFileName = file.name;
            this.fileError = '';
        }
    }" class="mx-auto max-w-7xl space-y-6">
        @if (session('success'))
            <div x-cloak x-show="toastMessage" x-transition role="status" aria-live="polite"
                class="fixed right-4 top-20 z-50 flex max-w-md items-start gap-3 rounded-xl border border-emerald-200 bg-white px-4 py-3 text-sm text-emerald-800 shadow-lg dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <svg class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" />
                </svg>
                <p x-text="toastMessage"></p>
                <button type="button" @click="toastMessage = ''" class="ml-auto rounded p-1 text-slate-500 hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:text-gray-300 dark:hover:bg-gray-700" aria-label="Tutup notifikasi">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" aria-labelledby="notula-errors-heading"
                class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <h2 id="notula-errors-heading" class="font-semibold">Periksa kembali data yang dimasukkan.</h2>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <header class="flex items-center gap-4">
            <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-slate-100 text-slate-700 dark:bg-gray-800 dark:text-gray-100">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M7 3h7l5 5v13H5V3h2Zm7 0v5h5M8 13h8m-8 4h8" />
                </svg>
            </span>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-gray-100">Generate Notula Monitoring Kinerja</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-300">Upload kertas kerja Excel, unduh notula triwulanan.</p>
            </div>
        </header>

        <aside role="status" class="flex flex-wrap items-center gap-2 rounded-xl border border-green-200 bg-green-100 px-4 py-3 text-sm font-medium text-green-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <svg class="size-5 shrink-0 text-green-700 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75m6.75 2.25a9.75 9.75 0 1 1-19.5 0 9.75 9.75 0 0 1 19.5 0Z" />
            </svg>
            <span>Lingkungan siap — mesin dokumen PHP {{ $phpVersion }}, batas upload PHP ini {{ $uploadLimit }} (validasi aplikasi maksimum 1024M).</span>
        </aside>

        <section aria-labelledby="notula-source-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <header class="border-b border-slate-100 px-5 py-4 dark:border-gray-700">
                <h3 id="notula-source-heading" class="text-base font-bold text-slate-800 dark:text-gray-100">1. Pilih Sumber Data</h3>
            </header>

            <div class="px-5 pt-4">
                <div role="tablist" aria-label="Sumber data notula" class="flex gap-2 overflow-x-auto border-b border-slate-200 dark:border-gray-700">
                    <button type="button" role="tab" id="notula-tab-app" x-ref="app"
                        aria-selected="{{ $selectedSource === 'app' ? 'true' : 'false' }}"
                        :aria-selected="(tab === 'app').toString()"
                        tabindex="{{ $selectedSource === 'app' ? '0' : '-1' }}"
                        :tabindex="tab === 'app' ? 0 : -1"
                        @click="tab = 'app'"
                        @keydown.right.prevent="selectTab('excel')"
                        @keydown.left.prevent="selectTab('excel')"
                        class="min-h-11 shrink-0 border-b-2 border-transparent px-4 text-sm font-semibold text-slate-500 transition-colors hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 aria-selected:border-blue-700 aria-selected:text-blue-700 dark:text-gray-300 dark:hover:text-white dark:aria-selected:border-gray-100 dark:aria-selected:text-gray-100">
                        Dari Data Aplikasi
                    </button>
                    <button type="button" role="tab" id="notula-tab-excel" x-ref="excel"
                        aria-selected="{{ $selectedSource === 'excel' ? 'true' : 'false' }}"
                        :aria-selected="(tab === 'excel').toString()"
                        tabindex="{{ $selectedSource === 'excel' ? '0' : '-1' }}"
                        :tabindex="tab === 'excel' ? 0 : -1"
                        @click="tab = 'excel'"
                        @keydown.right.prevent="selectTab('app')"
                        @keydown.left.prevent="selectTab('app')"
                        class="min-h-11 shrink-0 border-b-2 border-transparent px-4 text-sm font-semibold text-slate-500 transition-colors hover:text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 aria-selected:border-blue-700 aria-selected:text-blue-700 dark:text-gray-300 dark:hover:text-white dark:aria-selected:border-gray-100 dark:aria-selected:text-gray-100">
                        Upload Excel
                    </button>
                </div>
            </div>

            <section x-show="tab === 'app'" x-transition.opacity
                @if ($selectedSource !== 'app') hidden @endif
                :hidden="tab !== 'app'" id="notula-panel-app" role="tabpanel" aria-labelledby="notula-tab-app"
                class="space-y-4 p-5">
                <search>
                    <form action="{{ route('notula.index') }}" method="GET" aria-label="Pilih tahun dan triwulan"
                        class="flex flex-wrap items-end gap-3">
                        <input type="hidden" name="source" value="app">
                        <div class="grid min-w-40 gap-1.5">
                            <label for="notula-tahun" class="text-sm font-medium text-slate-700 dark:text-gray-200">Tahun</label>
                            <select id="notula-tahun" name="tahun" class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid min-w-48 gap-1.5">
                            <label for="notula-triwulan" class="text-sm font-medium text-slate-700 dark:text-gray-200">Triwulan</label>
                            <select id="notula-triwulan" name="triwulan" class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @foreach ($quarters as $value => $label)
                                    <option value="{{ $value }}" @selected($selectedQuarter === $value)>
                                        {{ $label }}{{ $selectedYear === $activePeriod?->tahun && $value === $activePeriod?->triwulan ? ' (aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992V4.356M20.96 9.36a9 9 0 1 0 2.03 5.64" />
                            </svg>
                            Muat Ulang
                        </button>
                    </form>
                </search>
                <p class="text-sm text-slate-500 dark:text-gray-300">
                    Mengambil langsung dari Entry Capaian Kinerja yang sudah diisi di aplikasi. Tidak perlu unduh atau unggah apa pun.
                </p>
            </section>

            <section x-show="tab === 'excel'" x-transition.opacity
                @if ($selectedSource !== 'excel') hidden @endif
                :hidden="tab !== 'excel'" id="notula-panel-excel" role="tabpanel" aria-labelledby="notula-tab-excel"
                class="space-y-4 p-5">
                <form action="{{ route('notula.uploadExcel') }}" method="POST" enctype="multipart/form-data"
                    aria-label="Unggah kertas kerja Excel" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                    <input type="hidden" name="triwulan" value="{{ $selectedQuarter }}">
                    <input type="hidden" name="source" value="excel">

                    <label for="notula-excel-file"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleDrop($event)"
                        :class="dragOver ? 'border-blue-500 bg-blue-50 dark:bg-gray-700' : 'border-slate-300 bg-slate-50 dark:border-gray-600 dark:bg-gray-900'"
                        class="group flex min-h-44 cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed p-6 text-center transition focus-within:outline focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-blue-600 sm:p-8">
                        <input id="notula-excel-file" x-ref="excelInput" type="file" name="file" accept=".xlsx"
                            @change="updateFile($event)"
                            aria-describedby="notula-excel-help notula-excel-selected notula-excel-error"
                            class="sr-only">
                        <span class="grid size-12 place-items-center rounded-full bg-white text-slate-600 shadow-sm dark:bg-gray-800 dark:text-gray-100">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m0 0L7 9m5-5 5 5M4 16v4h16v-4" />
                            </svg>
                        </span>
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-100">Tarik dan lepas file Excel (.xlsx) di sini, atau klik untuk memilih file.</span>
                        <span class="text-xs text-slate-500 dark:text-gray-300">Batas maksimum validasi aplikasi 1024M.</span>
                    </label>

                    <p id="notula-excel-help" class="text-sm text-slate-500 dark:text-gray-300">Pastikan file sesuai dengan template kertas kerja yang disediakan.</p>
                    <p id="notula-excel-selected" x-show="selectedFileName" class="text-sm font-medium text-blue-700 dark:text-blue-300">
                        File dipilih: <span x-text="selectedFileName"></span>
                    </p>
                    @if ($uploadedExcelName)
                        <p class="text-xs text-slate-500 dark:text-gray-300">Unggahan tersimpan privat: {{ $uploadedExcelName }}</p>
                    @endif
                    <p id="notula-excel-error" x-show="fileError" role="alert" class="text-sm text-rose-700 dark:text-rose-300" x-text="fileError"></p>
                    @error('file')
                        <p role="alert" class="text-sm text-rose-700 dark:text-rose-300">{{ $message }}</p>
                    @enderror

                    <button x-show="selectedFileName" type="submit"
                        class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16.5V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2.5M12 3v12m0 0 4-4m-4 4-4-4" />
                        </svg>
                        Unggah File Excel
                    </button>
                </form>
                <p class="rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-800 dark:bg-gray-700 dark:text-gray-100">
                    File disimpan di penyimpanan privat. Pratinjau sementara tetap memakai data capaian aplikasi; pembacaan template Excel belum diaktifkan.
                </p>
            </section>
        </section>

        <section aria-labelledby="notula-meeting-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <header class="border-b border-slate-100 px-5 py-4 dark:border-gray-700">
                <h3 id="notula-meeting-heading" class="text-base font-bold text-slate-800 dark:text-gray-100">2. Informasi Rapat</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-300">Data ini disimpan untuk sesi Anda dan digunakan saat membuat notula.</p>
            </header>
            <form action="{{ route('notula.storeRapat') }}" method="POST" aria-labelledby="notula-meeting-heading" class="space-y-5 p-5">
                @csrf
                <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                <input type="hidden" name="triwulan" value="{{ $selectedQuarter }}">

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="grid gap-1.5">
                        <label for="notula-satuan-kerja" class="text-sm font-medium text-slate-700 dark:text-gray-200">Satuan Kerja <span aria-hidden="true">*</span></label>
                        <input id="notula-satuan-kerja" name="satuan_kerja" type="text" maxlength="255" required
                            value="{{ old('satuan_kerja', $meetingInfo['satuan_kerja'] ?? 'BPS Provinsi Kalimantan Timur') }}"
                            @if ($errors->has('satuan_kerja')) autofocus aria-invalid="true" aria-describedby="notula-satuan-kerja-error" @endif
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @error('satuan_kerja')
                            <p id="notula-satuan-kerja-error" class="text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-nilai-sakip" class="text-sm font-medium text-slate-700 dark:text-gray-200">Nilai SAKIP</label>
                        <input id="notula-nilai-sakip" name="nilai_sakip" type="text" maxlength="100" value="{{ old('nilai_sakip', $meetingInfo['nilai_sakip'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-predikat" class="text-sm font-medium text-slate-700 dark:text-gray-200">Predikat</label>
                        <input id="notula-predikat" name="predikat" type="text" maxlength="100" value="{{ old('predikat', $meetingInfo['predikat'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-tanggal-rapat" class="text-sm font-medium text-slate-700 dark:text-gray-200">Hari/Tanggal Rapat</label>
                        <input id="notula-tanggal-rapat" name="tanggal_rapat" type="date" value="{{ old('tanggal_rapat', $meetingInfo['tanggal_rapat'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-waktu" class="text-sm font-medium text-slate-700 dark:text-gray-200">Waktu</label>
                        <input id="notula-waktu" name="waktu" type="text" maxlength="100" placeholder="09.00 WITA — selesai" value="{{ old('waktu', $meetingInfo['waktu'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-tempat" class="text-sm font-medium text-slate-700 dark:text-gray-200">Tempat</label>
                        <input id="notula-tempat" name="tempat" type="text" maxlength="255" placeholder="Ruang Rapat BPS Provinsi Kalimantan Timur" value="{{ old('tempat', $meetingInfo['tempat'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-pimpinan-rapat" class="text-sm font-medium text-slate-700 dark:text-gray-200">Pimpinan Rapat</label>
                        <input id="notula-pimpinan-rapat" name="pimpinan_rapat" type="text" maxlength="255" placeholder="Kepala BPS Provinsi Kalimantan Timur" value="{{ old('pimpinan_rapat', $meetingInfo['pimpinan_rapat'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-nama-kepala" class="text-sm font-medium text-slate-700 dark:text-gray-200">Nama Kepala Satker</label>
                        <input id="notula-nama-kepala" name="nama_kepala_satker" type="text" maxlength="255" value="{{ old('nama_kepala_satker', $meetingInfo['nama_kepala_satker'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-nama-notulis" class="text-sm font-medium text-slate-700 dark:text-gray-200">Nama Notulis</label>
                        <input id="notula-nama-notulis" name="nama_notulis" type="text" maxlength="255" value="{{ old('nama_notulis', $meetingInfo['nama_notulis'] ?? '') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                    <div class="grid gap-1.5">
                        <label for="notula-kota" class="text-sm font-medium text-slate-700 dark:text-gray-200">Kota (blok TTD)</label>
                        <input id="notula-kota" name="kota" type="text" maxlength="100" value="{{ old('kota', $meetingInfo['kota'] ?? 'Samarinda') }}"
                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-gray-700">
                    <button type="submit" class="min-h-11 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                        Simpan Informasi Rapat
                    </button>
                </div>
            </form>
        </section>

        <section aria-labelledby="notula-preview-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <header class="border-b border-slate-100 px-5 py-4 dark:border-gray-700">
                <h3 id="notula-preview-heading" class="text-base font-bold text-slate-800 dark:text-gray-100">4. Cek Data Hasil Pembacaan</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-300">{{ $quarters[$selectedQuarter] }} {{ $selectedYear }} · Sumber: Data aplikasi</p>
            </header>

            <div class="grid gap-3 p-5 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-lg bg-blue-50 p-4 text-blue-800 dark:bg-gray-700 dark:text-gray-100">
                    <p class="text-xs font-bold uppercase tracking-wide">IKU Terbaca</p>
                    <p class="mt-1 text-2xl font-bold">{{ $counts['read'] }}</p>
                </article>
                <article class="rounded-lg bg-green-50 p-4 text-green-800 dark:bg-gray-700 dark:text-gray-100">
                    <p class="text-xs font-bold uppercase tracking-wide">Lengkap</p>
                    <p class="mt-1 text-2xl font-bold">{{ $counts['complete'] }}</p>
                </article>
                <article class="rounded-lg bg-yellow-50 p-4 text-yellow-800 dark:bg-gray-700 dark:text-gray-100">
                    <p class="text-xs font-bold uppercase tracking-wide">Sebagian</p>
                    <p class="mt-1 text-2xl font-bold">{{ $counts['partial'] }}</p>
                </article>
                <article class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-gray-700 dark:text-gray-100">
                    <p class="text-xs font-bold uppercase tracking-wide">Kosong</p>
                    <p class="mt-1 text-2xl font-bold">{{ $counts['empty'] }}</p>
                </article>
            </div>

            <div tabindex="0" role="region" aria-labelledby="notula-preview-caption"
                class="max-w-full overflow-x-auto border-t border-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-700">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <caption id="notula-preview-caption" class="sr-only">Pratinjau hasil pembacaan IKU {{ $selectedYear }} {{ $selectedQuarter }}</caption>
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                        <tr>
                            @foreach (['Kode', 'Indikator', 'Target PK', 'Target TW', 'Realisasi', 'Cap. TW', 'Cap. PK', 'Status'] as $heading)
                                <th scope="col" class="px-4 py-3">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                        @forelse ($ikssData as $item)
                            <tr class="text-slate-700 transition-colors hover:bg-slate-50 dark:text-gray-100 dark:hover:bg-gray-700/60">
                                <th scope="row" class="whitespace-nowrap px-4 py-3 font-medium">{{ $item['kode'] }}</th>
                                <td class="min-w-64 px-4 py-3 font-medium">{{ $item['indikator'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $item['target_pk'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $item['target_tw'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $item['realisasi'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $item['cap_tw'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $item['cap_pk'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if ($item['status'] === 'Lengkap')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700 dark:bg-gray-700 dark:text-gray-100">
                                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" />
                                            </svg>
                                            Lengkap
                                        </span>
                                    @elseif ($item['status'] === 'Sebagian')
                                        <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-800 dark:bg-gray-700 dark:text-gray-100">Sebagian</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-gray-700 dark:text-gray-100">Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500 dark:text-gray-300">
                                    Belum ada data IKSS untuk {{ $quarters[$selectedQuarter] }} {{ $selectedYear }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section aria-labelledby="notula-download-heading" class="overflow-hidden rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <header>
                <h3 id="notula-download-heading" class="text-base font-bold text-slate-800 dark:text-gray-100">5. Unduh Notula</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-gray-300">Pilih dokumen yang akan dibuat untuk {{ $quarters[$selectedQuarter] }} {{ $selectedYear }}.</p>
            </header>

            <div class="mt-4 flex flex-wrap gap-3">
                <form action="{{ route('notula.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                    <input type="hidden" name="triwulan" value="{{ $selectedQuarter }}">
                    <input type="hidden" name="format" value="docx">
                    <button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4m-4 8h16" />
                        </svg>
                        Generate &amp; Unduh Notula (.docx)
                    </button>
                </form>
                <form action="{{ route('notula.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tahun" value="{{ $selectedYear }}">
                    <input type="hidden" name="triwulan" value="{{ $selectedQuarter }}">
                    <input type="hidden" name="format" value="xlsx">
                    <button type="submit" class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
                        Export Kertas Kerja (.xlsx)
                    </button>
                </form>
                <button type="button" disabled aria-describedby="notula-sources-note"
                    class="inline-flex min-h-11 cursor-not-allowed items-center rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-500">
                    Dokumen Sumber — Semua IKU
                </button>
                <button type="button" disabled aria-describedby="notula-sources-note"
                    class="inline-flex min-h-11 cursor-not-allowed items-center rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-500">
                    Dokumen Sumber TW Sebelumnya — Semua IKU
                </button>
                <button type="button" disabled aria-describedby="notula-sources-note"
                    class="inline-flex min-h-11 cursor-not-allowed items-center rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-500">
                    Unduh Semua Dokumen (.ZIP)
                </button>
            </div>
            <p id="notula-sources-note" class="mt-3 text-xs text-slate-500 dark:text-gray-300">
                Unduhan dokumen sumber belum tersedia karena data aplikasi belum menyimpan berkas sumber per IKU.
            </p>
        </section>
    </div>
@endsection
