<dialog
    x-ref="dialog"
    aria-labelledby="capaian-modal-title"
    @cancel="handleCancel($event)"
    @close="afterClose()"
    closedby="closerequest"
    class="m-auto h-[96dvh] max-h-[96dvh] w-[calc(100%-1rem)] max-w-7xl overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 sm:w-[calc(100%-2rem)]"
>
    <div class="flex h-full min-h-0 flex-col">
        <header class="shrink-0 border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-5 px-5 py-5 sm:px-8">
                <div class="min-w-0">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-bold tracking-wide text-orange-800 dark:bg-orange-950 dark:text-orange-200">
                            IKU <span x-text="ikss.kode_indikator || '—'"></span> · IKU · TAHUNAN
                        </span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                            x-text="periode.tahun ? `${periode.tahun} · ${quarterLabel(activeQuarter)}` : 'Memuat periode'"></span>
                    </div>
                    <h1 id="capaian-modal-title" class="max-w-5xl text-xl font-bold leading-tight text-slate-900 dark:text-white sm:text-2xl"
                        x-text="ikss.indikator_kinerja || 'Edit Capaian Kinerja'"></h1>
                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500 dark:text-slate-400">
                        <p class="inline-flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 0 4 22.5v-17Z M4 18.5A2.5 2.5 0 0 1 6.5 16H20" />
                            </svg>
                            <span x-text="`${(ikss.kode_indikator || '').replace(/\.\d+$/, '')} — ${ikss.sasaran_kegiatan || 'Sasaran kegiatan belum tersedia'}`"></span>
                        </p>
                        <span class="inline-flex items-center gap-2">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V5m0 14h16M8 15l3-4 3 2 5-6" />
                            </svg>
                            Persentase (X/Y)
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                                <path stroke-linecap="round" stroke-width="1.8" d="M12 7v5l3 2" />
                            </svg>
                            Tahunan
                        </span>
                    </div>
                </div>
                <button type="button" @click="close()" aria-label="Tutup modal edit capaian"
                    class="grid size-10 shrink-0 place-items-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:hover:bg-slate-800 dark:hover:text-white">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 px-5 py-3 dark:border-slate-800 sm:px-8">
                <div class="flex flex-wrap gap-2 text-sm">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Target Tahunan:</span>
                        <strong class="ml-1 text-slate-800 dark:text-slate-100" x-text="targetAnnual"></strong>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
                        <span class="text-slate-500 dark:text-slate-400">Alokasi <span x-text="quarterLabel(activeQuarter)"></span>:</span>
                        <strong class="ml-1 text-slate-800 dark:text-slate-100" x-text="quarterAllocation"></strong>
                    </div>
                </div>
                <button type="button" @click="generateDocument()"
                    class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v3h14v-3" />
                    </svg>
                    Generate Dokumen Sumber Tindak Lanjut TW IV
                </button>
            </div>

            <nav aria-label="Bagian form capaian" class="overflow-x-auto border-t border-slate-200 px-5 dark:border-slate-700 sm:px-8">
                <div class="flex min-w-max gap-2">
                    <button type="button" @click="tab = 'capaian'" :aria-pressed="tab === 'capaian'"
                        :class="tab === 'capaian' ? 'border-orange-600 text-orange-700 dark:text-orange-300' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100'"
                        class="min-h-12 border-b-2 px-3 text-sm font-semibold transition-colors">
                        Capaian IKU
                    </button>
                    <button type="button" @click="tab = 'analisis'" :aria-pressed="tab === 'analisis'"
                        :class="tab === 'analisis' ? 'border-orange-600 text-orange-700 dark:text-orange-300' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100'"
                        class="min-h-12 border-b-2 px-3 text-sm font-semibold transition-colors">
                        Analisis Capaian IKU
                    </button>
                    <button type="button" @click="tab = 'ro'" :aria-pressed="tab === 'ro'"
                        :class="tab === 'ro' ? 'border-orange-600 text-orange-700 dark:text-orange-300' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100'"
                        class="min-h-12 border-b-2 px-3 text-sm font-semibold transition-colors">
                        Capaian RO
                    </button>
                    <button type="button" @click="tab = 'tindak-lanjut'" :aria-pressed="tab === 'tindak-lanjut'"
                        :class="tab === 'tindak-lanjut' ? 'border-orange-600 text-orange-700 dark:text-orange-300' : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100'"
                        class="min-h-12 border-b-2 px-3 text-sm font-semibold transition-colors">
                        Tindak Lanjut TW Sebelumnya
                    </button>
                </div>
            </nav>
        </header>

        <form @submit.prevent="save()" class="flex min-h-0 flex-1 flex-col">
            <div class="min-h-0 flex-1 overflow-y-auto bg-slate-50/70 px-5 py-6 dark:bg-slate-950/40 sm:px-8">
                <div x-show="loading" role="status" class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-900 dark:bg-blue-950/50 dark:text-blue-200">
                    Memuat data indikator...
                </div>
                <div x-show="error" x-cloak role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-200" x-text="error"></div>
                <div x-show="message" x-cloak role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200" x-text="message"></div>

                <section x-show="tab === 'capaian'" x-cloak aria-labelledby="modal-capaian-heading" class="space-y-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-700 dark:text-orange-300">Bagian 1</p>
                        <h2 id="modal-capaian-heading" class="mt-1 text-lg font-bold text-slate-900 dark:text-white">Data Capaian IKU</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Isi X (jumlah yang berkualitas) dan Y (jumlah seluruhnya) untuk setiap triwulan.</p>
                    </div>

                    <fieldset class="space-y-3">
                        <legend class="text-sm font-bold text-slate-800 dark:text-slate-100">X — Jumlah yang berkualitas</legend>
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <template x-for="quarter in ['tw1', 'tw2', 'tw3', 'tw4']" :key="`x-${quarter}`">
                                <div :class="activeQuarter === quarter ? 'border-orange-400 ring-1 ring-orange-200 dark:border-orange-600 dark:ring-orange-900' : 'border-slate-200 dark:border-slate-700'"
                                    class="rounded-xl border bg-white p-4 shadow-sm dark:bg-slate-900">
                                    <div class="mb-4 flex items-center justify-between gap-2">
                                        <h3 class="font-semibold text-slate-800 dark:text-slate-100" x-text="quarterLabel(quarter)"></h3>
                                        <span x-show="activeQuarter === quarter" class="rounded-full bg-orange-100 px-2 py-1 text-[10px] font-bold tracking-wide text-orange-800 dark:bg-orange-950 dark:text-orange-200">AKTIF</span>
                                    </div>
                                    <label :for="`x-${quarter}`" class="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-300">Jumlah X</label>
                                    <input :id="`x-${quarter}`" :autofocus="quarter === activeQuarter" type="number" min="0" step="any"
                                        x-model.number="form.capaian_iku[quarter].x"
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-orange-600 dark:border-slate-600 dark:bg-slate-950 dark:text-white">
                                </div>
                            </template>
                        </div>
                    </fieldset>

                    <fieldset class="space-y-3">
                        <legend class="text-sm font-bold text-slate-800 dark:text-slate-100">Y — Jumlah seluruhnya</legend>
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <template x-for="quarter in ['tw1', 'tw2', 'tw3', 'tw4']" :key="`y-${quarter}`">
                                <div :class="activeQuarter === quarter ? 'border-orange-400 ring-1 ring-orange-200 dark:border-orange-600 dark:ring-orange-900' : 'border-slate-200 dark:border-slate-700'"
                                    class="rounded-xl border bg-white p-4 shadow-sm dark:bg-slate-900">
                                    <div class="mb-4 flex items-center justify-between">
                                        <h3 class="font-semibold text-slate-800 dark:text-slate-100" x-text="quarterLabel(quarter)"></h3>
                                        <span class="text-xs font-medium text-slate-400" x-text="`${percentage(quarter)}%`"></span>
                                    </div>
                                    <label :for="`y-${quarter}`" class="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-300">Jumlah Y</label>
                                    <input :id="`y-${quarter}`" type="number" min="0" step="any" x-model.number="form.capaian_iku[quarter].y"
                                        class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-orange-600 dark:border-slate-600 dark:bg-slate-950 dark:text-white">
                                    <p class="mt-3 border-t border-slate-100 pt-2 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                        Capaian <span x-text="quarterLabel(quarter)"></span>:
                                        <strong class="text-slate-800 dark:text-slate-100" x-text="`${percentage(quarter)}%`"></strong>
                                    </p>
                                </div>
                            </template>
                        </div>
                    </fieldset>
                </section>

                <section x-show="tab === 'analisis'" x-cloak aria-labelledby="modal-analisis-heading" class="space-y-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-700 dark:text-orange-300">Bagian 2</p>
                        <h2 id="modal-analisis-heading" class="mt-1 text-lg font-bold text-slate-900 dark:text-white">Analisis Pencapaian <span x-text="quarterLabel(activeQuarter)"></span></h2>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="grid gap-2 lg:col-span-2">
                            <label for="analisis-kendala" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Kendala yang Dihadapi</label>
                            <textarea id="analisis-kendala" rows="5" maxlength="10000" x-model="form.analisis_capaian.kendala"
                                placeholder="Sakernas Februari 2026... 1. Pada hari pertama..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-orange-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></textarea>
                        </div>
                        <div class="grid gap-2 lg:col-span-2">
                            <label for="analisis-solusi" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Solusi yang Sudah Dilakukan</label>
                            <textarea id="analisis-solusi" rows="5" maxlength="10000" x-model="form.analisis_capaian.solusi"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-orange-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></textarea>
                        </div>
                    </div>

                    <section aria-labelledby="bukti-solusi-heading" class="space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 id="bukti-solusi-heading" class="text-sm font-semibold text-slate-800 dark:text-slate-100">Bukti Dokumentasi Solusi</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Opsional — foto per poin solusi.</p>
                            </div>
                            <button type="button" @click="addSolutionPoint()" class="min-h-10 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-white dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                + Tambah Poin Solusi
                            </button>
                        </div>
                        <template x-for="(point, index) in form.analisis_capaian.solution_points" :key="`solution-${index}`">
                            <article class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900 sm:grid-cols-[minmax(0,1fr)_auto]">
                                <div class="grid gap-2">
                                    <label :for="`solution-point-${index}`" class="text-xs font-semibold text-slate-600 dark:text-slate-300">Uraian poin solusi</label>
                                    <input :id="`solution-point-${index}`" type="text" maxlength="2000" x-model="point.uraian"
                                        class="min-h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-950">
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <template x-for="photo in point.photos" :key="photo.id">
                                            <a :href="photo.url" target="_blank" rel="noreferrer" class="rounded bg-slate-100 px-2 py-1 text-blue-700 underline dark:bg-slate-800 dark:text-blue-300" x-text="photo.name"></a>
                                        </template>
                                        <template x-for="(file, fileIndex) in pendingSolutionPhotos[index] || []" :key="`${file.name}-${fileIndex}`">
                                            <button type="button" @click="removePending('pendingSolutionPhotos', index, fileIndex)" class="rounded bg-orange-50 px-2 py-1 text-orange-800 dark:bg-orange-950 dark:text-orange-200" x-text="`${file.name} ×`"></button>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-end gap-2">
                                    <input :id="`solution-photo-${index}`" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only"
                                        @change="addSolutionPhotos($event, index)">
                                    <label :for="`solution-photo-${index}`" class="inline-flex min-h-10 cursor-pointer items-center gap-2 rounded-lg border border-dashed border-slate-400 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-orange-500 hover:text-orange-700 dark:border-slate-600 dark:text-slate-200 dark:hover:text-orange-300">
                                        Tambah Foto
                                    </label>
                                    <button type="button" x-show="form.analisis_capaian.solution_points.length > 1" @click="removeSolutionPoint(index)" aria-label="Hapus poin solusi"
                                        class="grid size-10 place-items-center rounded-lg text-red-600 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-950">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-10 0 1 13h6l1-13M9 7V4h6v3" />
                                        </svg>
                                    </button>
                                </div>
                            </article>
                        </template>
                    </section>

                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="grid gap-2 lg:col-span-2">
                            <label for="rencana-tindak-lanjut" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Rencana Tindak Lanjut (RTL)</label>
                            <textarea id="rencana-tindak-lanjut" rows="4" maxlength="10000" x-model="form.analisis_capaian.rencana_tindak_lanjut"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-orange-600 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></textarea>
                        </div>
                        <div class="grid gap-2">
                            <label for="pic-tindak-lanjut" class="text-sm font-semibold text-slate-700 dark:text-slate-200">PIC Tindak Lanjut</label>
                            <input id="pic-tindak-lanjut" type="text" maxlength="255" x-model="form.analisis_capaian.pic"
                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-900">
                        </div>
                        <div class="grid gap-2">
                            <label for="batas-waktu-tl" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Batas Waktu TL</label>
                            <input id="batas-waktu-tl" type="date" x-model="form.analisis_capaian.batas_waktu"
                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-900">
                        </div>
                        <div class="lg:col-span-2">
                            <button type="button" @click="copyFromIkss()"
                                class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3M9 3h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" />
                                </svg>
                                Ambil dari IKSS
                            </button>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Menyalin sasaran kegiatan IKSS ke kolom RTL.</p>
                        </div>
                        <div class="grid gap-2">
                            <label for="link-bukti-kinerja" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Link Bukti Dukung Kinerja</label>
                            <input id="link-bukti-kinerja" type="url" maxlength="2048" x-model="form.analisis_capaian.link_bukti_kinerja"
                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-900">
                        </div>
                        <div class="grid gap-2">
                            <label for="link-bukti-tl" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Link Bukti Dukung TL Sebelumnya</label>
                            <input id="link-bukti-tl" type="url" maxlength="2048" x-model="form.analisis_capaian.link_bukti_tl_sebelumnya"
                                class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-900">
                        </div>
                    </div>
                </section>

                <section x-show="tab === 'ro'" x-cloak aria-labelledby="modal-ro-heading" class="space-y-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-700 dark:text-orange-300">Bagian 3</p>
                        <h2 id="modal-ro-heading" class="mt-1 text-lg font-bold text-slate-900 dark:text-white">Rincian Output</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sub-sheet <span x-text="ikss.kode_indikator || 'IKSS'"></span></p>
                    </div>

                    <template x-for="(row, rowIndex) in form.capaian_ro" :key="row.kode">
                        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                            <div class="border-b border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-700 dark:bg-slate-800/70 sm:px-5">
                                <p class="text-xs font-bold uppercase tracking-wide text-orange-700 dark:text-orange-300" x-text="row.kode"></p>
                                <h3 class="mt-1 font-semibold text-slate-900 dark:text-white" x-text="row.nama"></h3>
                            </div>
                            <div class="grid gap-5 p-4 sm:p-5">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="grid gap-2">
                                        <label :for="`ro-vol-${rowIndex}`" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Realisasi VOL</label>
                                        <input :id="`ro-vol-${rowIndex}`" type="number" min="0" step="any" x-model.number="row.vol"
                                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-950">
                                    </div>
                                    <div class="grid gap-2">
                                        <label :for="`ro-progress-${rowIndex}`" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Progres (%)</label>
                                        <input :id="`ro-progress-${rowIndex}`" type="number" min="0" max="100" step="0.01" x-model.number="row.progres"
                                            class="min-h-11 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-950">
                                    </div>
                                </div>
                                <div class="grid gap-2">
                                    <label :for="`ro-narasi-${rowIndex}`" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Narasi Realisasi</label>
                                    <textarea :id="`ro-narasi-${rowIndex}`" rows="4" maxlength="10000" x-model="row.narasi"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-950"></textarea>
                                </div>
                                <div class="grid gap-3">
                                    <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Bukti Foto & Laporan</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <input :id="`ro-photo-${rowIndex}`" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" @change="addRoPhotos($event, rowIndex)">
                                        <label :for="`ro-photo-${rowIndex}`" class="inline-flex min-h-10 cursor-pointer items-center gap-2 rounded-lg border border-dashed border-slate-400 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-orange-500 hover:text-orange-700 dark:border-slate-600 dark:text-slate-200">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5Zm3 12 3-3 2 2 3-4 3 5H7Z" />
                                            </svg>
                                            Tambah Foto
                                        </label>
                                        <input :id="`ro-pdf-${rowIndex}`" type="file" accept="application/pdf" class="sr-only" @change="addReport($event, rowIndex)">
                                        <label :for="`ro-pdf-${rowIndex}`" class="inline-flex min-h-10 cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                            Upload PDF Laporan
                                        </label>
                                    </div>
                                    <div class="flex flex-wrap gap-2 text-xs">
                                        <template x-for="photo in row.fotos" :key="photo.id">
                                            <a :href="photo.url" target="_blank" rel="noreferrer" class="rounded bg-slate-100 px-2 py-1 text-blue-700 underline dark:bg-slate-800 dark:text-blue-300" x-text="photo.name"></a>
                                        </template>
                                        <template x-for="(file, fileIndex) in pendingRoPhotos[rowIndex] || []" :key="`${file.name}-${fileIndex}`">
                                            <button type="button" @click="removePending('pendingRoPhotos', rowIndex, fileIndex)" class="rounded bg-orange-50 px-2 py-1 text-orange-800 dark:bg-orange-950 dark:text-orange-200" x-text="`${file.name} ×`"></button>
                                        </template>
                                        <a x-show="row.laporan_pdf" :href="row.laporan_pdf?.url" target="_blank" rel="noreferrer" class="rounded bg-blue-50 px-2 py-1 text-blue-800 underline dark:bg-blue-950 dark:text-blue-200" x-text="row.laporan_pdf?.name"></a>
                                        <span x-show="pendingReports[rowIndex]" class="rounded bg-orange-50 px-2 py-1 text-orange-800 dark:bg-orange-950 dark:text-orange-200" x-text="pendingReports[rowIndex]?.name"></span>
                                    </div>
                                </div>

                                <section class="space-y-3 border-t border-slate-100 pt-4 dark:border-slate-800" :aria-labelledby="`ro-activity-title-${rowIndex}`">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <h4 :id="`ro-activity-title-${rowIndex}`" class="text-sm font-semibold text-slate-800 dark:text-slate-100">Uraian Kegiatan</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">Opsional — kegiatan, sub kegiatan, dan tahapan; foto bukti dipasang di tiap butir.</p>
                                        </div>
                                        <button type="button" @click="addRoActivity(rowIndex)" class="min-h-10 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                            + Tambah Kegiatan
                                        </button>
                                    </div>
                                    <template x-for="(activity, activityIndex) in row.kegiatan" :key="`activity-${rowIndex}-${activityIndex}`">
                                        <div class="grid gap-3 rounded-lg border border-slate-200 p-3 dark:border-slate-700 sm:grid-cols-2">
                                            <div class="grid gap-1.5">
                                                <label :for="`activity-${rowIndex}-${activityIndex}`" class="text-xs font-semibold">Kegiatan</label>
                                                <input :id="`activity-${rowIndex}-${activityIndex}`" type="text" x-model="activity.kegiatan" class="min-h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-950">
                                            </div>
                                            <div class="grid gap-1.5">
                                                <label :for="`subactivity-${rowIndex}-${activityIndex}`" class="text-xs font-semibold">Sub kegiatan</label>
                                                <input :id="`subactivity-${rowIndex}-${activityIndex}`" type="text" x-model="activity.sub_kegiatan" class="min-h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-950">
                                            </div>
                                            <div class="grid gap-1.5 sm:col-span-2">
                                                <label :for="`activity-stage-${rowIndex}-${activityIndex}`" class="text-xs font-semibold">Tahapan</label>
                                                <textarea :id="`activity-stage-${rowIndex}-${activityIndex}`" rows="2" x-model="activity.tahapan" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-950"></textarea>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 sm:col-span-2">
                                                <input :id="`activity-photo-${rowIndex}-${activityIndex}`" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" @change="addActivityPhotos($event, rowIndex, activityIndex)">
                                                <label :for="`activity-photo-${rowIndex}-${activityIndex}`" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg border border-dashed border-slate-400 px-3 py-1.5 text-xs font-semibold dark:border-slate-600">Tambah Foto</label>
                                                <template x-for="photo in activity.fotos" :key="photo.id">
                                                    <a :href="photo.url" target="_blank" rel="noreferrer" class="text-xs text-blue-700 underline dark:text-blue-300" x-text="photo.name"></a>
                                                </template>
                                                <template x-for="(file, fileIndex) in pendingActivityPhotos[`${rowIndex}-${activityIndex}`] || []" :key="`${file.name}-${fileIndex}`">
                                                    <button type="button" @click="removePendingActivity(rowIndex, activityIndex, fileIndex)" class="text-xs text-orange-800 underline dark:text-orange-300" x-text="`${file.name} ×`"></button>
                                                </template>
                                                <button type="button" @click="removeRoActivity(rowIndex, activityIndex)" class="ml-auto min-h-9 rounded-lg px-3 text-xs font-semibold text-red-700 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-950">Hapus kegiatan</button>
                                            </div>
                                        </div>
                                    </template>
                                </section>
                            </div>
                        </article>
                    </template>
                </section>

                <section x-show="tab === 'tindak-lanjut'" x-cloak aria-labelledby="modal-previous-heading" class="space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-orange-700 dark:text-orange-300">Bagian 4</p>
                            <h2 id="modal-previous-heading" class="mt-1 text-lg font-bold text-slate-900 dark:text-white">
                                Tindak Lanjut <span x-text="previous.label || 'TW Sebelumnya'"></span> <span x-text="previous.tahun || ''"></span>
                            </h2>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l2.5 1.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Riwayat — teks tak bisa diedit
                        </span>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kendala TW Lalu</h3>
                            <p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-700 dark:text-slate-200" x-text="previous.kendala || '(kosong)'"></p>
                        </article>
                        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Solusi TW Lalu</h3>
                            <p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-700 dark:text-slate-200" x-text="previous.solusi || '(kosong)'"></p>
                        </article>
                        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Rencana Tindak Lanjut</h3>
                            <p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-700 dark:text-slate-200" x-text="previous.rencana_tindak_lanjut || '(kosong)'"></p>
                        </article>
                        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">PIC & Batas Waktu</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-700 dark:text-slate-200">
                                <span x-text="previous.pic || '(kosong)'"></span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400" x-text="previous.batas_waktu || ''"></span>
                            </p>
                        </article>
                    </div>

                    <div class="max-w-xl rounded-xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-900">
                        <label for="progress-follow-up" class="text-sm font-semibold text-slate-800 dark:text-slate-100">Progres Pelaksanaan Tindak Lanjut (%)</label>
                        <p id="progress-follow-up-hint" class="mt-1 text-xs text-slate-500 dark:text-slate-400">Seberapa jauh Rencana Tindak Lanjut TW lalu sudah dilaksanakan?</p>
                        <div class="mt-3 flex items-center gap-3">
                            <input id="progress-follow-up" type="number" min="0" max="100" step="0.01" x-model.number="form.tindak_lanjut.progres_pelaksanaan" aria-describedby="progress-follow-up-hint"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-600 dark:bg-slate-950">
                            <span class="font-semibold text-slate-500">%</span>
                        </div>
                    </div>
                </section>
            </div>

            <footer class="flex shrink-0 flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-900 sm:px-8">
                <button type="button" @click="close()" class="inline-flex min-h-11 items-center gap-2 rounded-lg px-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m14 18-6-6 6-6M8 12h12" />
                    </svg>
                    Kembali ke List
                </button>
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-xs text-slate-500 dark:text-slate-400" x-text="dirty ? 'Ada perubahan belum tersimpan...' : 'Semua perubahan sudah tersimpan.'"></span>
                    <button type="submit" :disabled="saving || loading"
                        class="inline-flex min-h-12 items-center gap-2 rounded-lg bg-orange-600 px-7 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-orange-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <svg x-show="!saving" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3h12l4 4v14H3V5a2 2 0 0 1 2-2Zm2 0v6h10V3M7 21v-8h10v8" />
                        </svg>
                        <svg x-show="saving" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                        </svg>
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan Data'"></span>
                    </button>
                </div>
            </footer>
        </form>
    </div>
</dialog>
