import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('countUp', (target) => ({
    value: 0,

    init() {
        const finalValue = Math.max(0, Number(target) || 0);

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.value = finalValue;
            return;
        }

        const duration = 800;
        const startTime = performance.now();
        const animate = (currentTime) => {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            this.value = Math.round(finalValue * progress);

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    },
}));

Alpine.data('capaianModal', () => ({
    tab: 'capaian',
    loading: false,
    saving: false,
    dirty: false,
    saved: false,
    suppressDirty: true,
    error: '',
    message: '',
    ikss: {},
    periode: {},
    previous: {},
    updateUrl: '',
    activeQuarter: 'tw1',
    targetAnnual: '100.00 Persen',
    quarterAllocation: '0.00 Persen',
    form: {},
    pendingSolutionPhotos: [],
    pendingRoPhotos: [],
    pendingReports: [],
    pendingActivityPhotos: {},

    init() {
        this.form = this.emptyForm();
        this.$watch('form', () => {
            if (!this.loading && !this.saving && !this.suppressDirty) {
                this.dirty = true;
            }
        });
        this.$nextTick(() => {
            this.suppressDirty = false;
        });
    },

    emptyForm() {
        return {
            capaian_iku: {
                tw1: { x: 0, y: 0 },
                tw2: { x: 0, y: 0 },
                tw3: { x: 0, y: 0 },
                tw4: { x: 0, y: 0 },
            },
            analisis_capaian: {
                kendala: '',
                solusi: '',
                solution_points: [{ uraian: '', photos: [] }],
                rencana_tindak_lanjut: '',
                pic: '',
                batas_waktu: '',
                link_bukti_kinerja: '',
                link_bukti_tl_sebelumnya: '',
            },
            capaian_ro: [
                {
                    kode: '2905 BMA 004',
                    nama: 'Publikasi/Laporan Survei Angkatan Kerja Nasional',
                    vol: 0,
                    progres: 0,
                    narasi: '',
                    fotos: [],
                    laporan_pdf: null,
                    kegiatan: [],
                },
                {
                    kode: '2905 BMA 005',
                    nama: 'Publikasi/Laporan Statistik Kependudukan dan Ketenagakerjaan',
                    vol: 0,
                    progres: 0,
                    narasi: '',
                    fotos: [],
                    laporan_pdf: null,
                    kegiatan: [],
                },
            ],
            tindak_lanjut: { progres_pelaksanaan: 0 },
        };
    },

    async open(editUrl, updateUrl) {
        this.tab = 'capaian';
        this.error = '';
        this.message = '';
        this.saved = false;
        this.loading = true;
        this.dirty = false;
        this.updateUrl = updateUrl;
        this.$refs.dialog.showModal();

        try {
            const response = await fetch(editUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Data indikator gagal dimuat.');
            }

            this.ikss = result.data.ikss;
            this.periode = result.data.periode;
            this.previous = result.data.previous;
            this.activeQuarter = this.periode.triwulan.toLowerCase();
            this.targetAnnual = this.formatTarget(
                result.data.capaian?.target_pk || this.ikss.target || '100.00 Persen',
            );
            this.quarterAllocation = result.data.capaian?.target_tw
                ? `${Number(result.data.capaian.target_tw).toFixed(2)} Persen`
                : '0.00 Persen';
            this.suppressDirty = true;
            this.form = this.hydrate(result.data.capaian);
            this.pendingSolutionPhotos = [];
            this.pendingRoPhotos = [];
            this.pendingReports = [];
            this.pendingActivityPhotos = {};
            this.$nextTick(() => {
                this.suppressDirty = false;
                document.getElementById(`x-${this.activeQuarter}`)?.focus();
            });
        } catch (error) {
            this.error = error.message || 'Terjadi kesalahan saat memuat data.';
        } finally {
            this.loading = false;
            this.dirty = false;
        }
    },

    hydrate(capaian) {
        const empty = this.emptyForm();

        if (!capaian) {
            return empty;
        }

        const analysis = capaian.analisis_capaian || {};
        const rows = capaian.capaian_ro || [];

        return {
            capaian_iku: { ...empty.capaian_iku, ...(capaian.capaian_iku || {}) },
            analisis_capaian: {
                ...empty.analisis_capaian,
                ...analysis,
                solution_points: (analysis.solution_points || empty.analisis_capaian.solution_points)
                    .map((point) => ({ uraian: '', photos: [], ...point })),
            },
            capaian_ro: empty.capaian_ro.map((defaultRow, index) => ({
                ...defaultRow,
                ...(rows[index] || {}),
                fotos: rows[index]?.fotos || [],
                laporan_pdf: rows[index]?.laporan_pdf || null,
                kegiatan: (rows[index]?.kegiatan || []).map((activity) => ({
                    kegiatan: '',
                    sub_kegiatan: '',
                    tahapan: '',
                    fotos: [],
                    ...activity,
                })),
            })),
            tindak_lanjut: { ...empty.tindak_lanjut, ...(capaian.tindak_lanjut || {}) },
        };
    },

    percentage(quarter) {
        const values = this.form.capaian_iku?.[quarter] || { x: 0, y: 0 };
        const denominator = Number(values.y) || 0;

        return denominator > 0
            ? ((Number(values.x) || 0) / denominator * 100).toFixed(2)
            : '0.00';
    },

    formatTarget(value) {
        const target = String(value || '100.00 Persen');
        const match = target.match(/^\s*(\d+(?:[.,]\d+)?)(?:\s*(Persen|Poin|%))?\s*$/i);

        if (!match) {
            return target;
        }

        const unit = match[2] === '%' ? 'Persen' : (match[2] || 'Persen');

        return `${Number(match[1].replace(',', '.')).toFixed(2)} ${unit}`;
    },

    quarterLabel(quarter) {
        return { tw1: 'TW I', tw2: 'TW II', tw3: 'TW III', tw4: 'TW IV' }[quarter];
    },

    addSolutionPoint() {
        this.form.analisis_capaian.solution_points.push({ uraian: '', photos: [] });
    },

    removeSolutionPoint(index) {
        this.form.analisis_capaian.solution_points.splice(index, 1);
        this.pendingSolutionPhotos.splice(index, 1);
    },

    addRoActivity(rowIndex) {
        this.form.capaian_ro[rowIndex].kegiatan.push({
            kegiatan: '',
            sub_kegiatan: '',
            tahapan: '',
            fotos: [],
        });
    },

    removeRoActivity(rowIndex, activityIndex) {
        this.form.capaian_ro[rowIndex].kegiatan.splice(activityIndex, 1);
        delete this.pendingActivityPhotos[`${rowIndex}-${activityIndex}`];
    },

    addSolutionPhotos(event, index) {
        this.pendingSolutionPhotos[index] = [
            ...(this.pendingSolutionPhotos[index] || []),
            ...Array.from(event.target.files || []),
        ];
        this.dirty = true;
        event.target.value = '';
    },

    addRoPhotos(event, index) {
        this.pendingRoPhotos[index] = [
            ...(this.pendingRoPhotos[index] || []),
            ...Array.from(event.target.files || []),
        ];
        this.dirty = true;
        event.target.value = '';
    },

    addReport(event, index) {
        this.pendingReports[index] = event.target.files?.[0] || null;
        this.dirty = true;
        event.target.value = '';
    },

    addActivityPhotos(event, rowIndex, activityIndex) {
        const key = `${rowIndex}-${activityIndex}`;
        this.pendingActivityPhotos[key] = [
            ...(this.pendingActivityPhotos[key] || []),
            ...Array.from(event.target.files || []),
        ];
        this.dirty = true;
        event.target.value = '';
    },

    removePending(list, index, fileIndex) {
        this[list][index].splice(fileIndex, 1);
        this.dirty = true;
    },

    removePendingActivity(rowIndex, activityIndex, fileIndex) {
        this.pendingActivityPhotos[`${rowIndex}-${activityIndex}`].splice(fileIndex, 1);
        this.dirty = true;
    },

    copyFromIkss() {
        this.form.analisis_capaian.rencana_tindak_lanjut = this.ikss.sasaran_kegiatan || '';
        this.message = 'Sasaran kegiatan IKSS disalin ke rencana tindak lanjut.';
    },

    generateDocument() {
        this.error = 'Pembuatan dokumen sumber belum tersedia pada aplikasi.';
    },

    handleCancel(event) {
        if (this.dirty && !window.confirm('Perubahan belum disimpan. Tutup tanpa menyimpan?')) {
            event.preventDefault();
        }
    },

    close() {
        if (this.dirty && !window.confirm('Perubahan belum disimpan. Tutup tanpa menyimpan?')) {
            return;
        }

        this.$refs.dialog.close();
    },

    afterClose() {
        if (this.saved) {
            window.location.reload();
        }
    },

    async save() {
        this.saving = true;
        this.error = '';
        this.message = '';

        const payload = {
            ...this.form,
            tahun: this.periode.tahun,
            triwulan: this.periode.triwulan,
        };
        const body = new FormData();
        body.append('payload', JSON.stringify(payload));

        this.pendingSolutionPhotos.forEach((files, pointIndex) => {
            files.forEach((file) => body.append(
                `files[analisis_capaian][solution_points][${pointIndex}][photos][]`,
                file,
            ));
        });
        this.pendingRoPhotos.forEach((files, rowIndex) => {
            files.forEach((file) => body.append(`files[capaian_ro][${rowIndex}][fotos][]`, file));
        });
        this.pendingReports.forEach((file, rowIndex) => {
            if (file) {
                body.append(`files[capaian_ro][${rowIndex}][laporan_pdf]`, file);
            }
        });
        Object.entries(this.pendingActivityPhotos).forEach(([key, files]) => {
            const [rowIndex, activityIndex] = key.split('-');
            files.forEach((file) => body.append(
                `files[capaian_ro][${rowIndex}][kegiatan][${activityIndex}][fotos][]`,
                file,
            ));
        });
        body.append('_method', 'PUT');

        try {
            const response = await fetch(this.updateUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                credentials: 'same-origin',
                body,
            });
            const result = await response.json();

            if (!response.ok) {
                const messages = Object.values(result.errors || {}).flat();
                throw new Error(messages[0] || result.message || 'Data gagal disimpan.');
            }

            this.suppressDirty = true;
            this.form = this.hydrate(result.data.capaian);
            this.pendingSolutionPhotos = [];
            this.pendingRoPhotos = [];
            this.pendingReports = [];
            this.pendingActivityPhotos = {};
            this.dirty = false;
            this.saved = true;
            this.message = result.message;
            this.$nextTick(() => {
                this.suppressDirty = false;
            });
        } catch (error) {
            this.error = error.message || 'Terjadi kesalahan saat menyimpan data.';
        } finally {
            this.saving = false;
        }
    },
}));

Alpine.start();
