<input type="hidden" name="_dialog" value="{{ $dialogKey }}">
<input type="hidden" name="tahun" value="{{ $tahun }}">
<input type="hidden" name="triwulan" value="{{ $triwulan }}">

@php
    $codePrefix = explode('.', (string) $ikssMaster->kode_indikator)[0];
    $defaultSasaranStrategis = $ikssMaster->sasaran_strategis
        ?? (in_array($codePrefix, ['1', '2', '3'], true) ? 'T'.$codePrefix : 'T1');
    $selectedSasaranStrategis = old('sasaran_strategis', $defaultSasaranStrategis);
@endphp

<fieldset class="grid gap-4 sm:grid-cols-2">
    <legend class="sr-only">Informasi indikator</legend>

    <div class="grid gap-1.5">
        <label for="{{ $formId }}-kode-indikator" class="text-sm font-semibold text-slate-700 dark:text-slate-200">
            Kode IKSS <span class="text-orange-600" aria-hidden="true">*</span>
        </label>
        <input id="{{ $formId }}-kode-indikator" name="kode_indikator" type="text" maxlength="20" required autofocus
            value="{{ old('kode_indikator', $ikssMaster->kode_indikator) }}"
            @if ($errors->has('kode_indikator')) aria-invalid="true" aria-describedby="{{ $formId }}-kode-indikator-error" @endif
            class="min-h-12 rounded-xl border border-slate-300 bg-white px-3 text-base text-slate-800 shadow-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        @error('kode_indikator')
            <p id="{{ $formId }}-kode-indikator-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-1.5">
        <label for="{{ $formId }}-sasaran-strategis" class="text-sm font-semibold text-slate-700 dark:text-slate-200">
            Sasaran Strategis <span class="text-orange-600" aria-hidden="true">*</span>
        </label>
        <select id="{{ $formId }}-sasaran-strategis" name="sasaran_strategis" required
            @if ($errors->has('sasaran_strategis')) aria-invalid="true" aria-describedby="{{ $formId }}-sasaran-strategis-error" @endif
            class="min-h-12 rounded-xl border border-slate-300 bg-white px-3 text-base text-slate-800 shadow-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            <option value="T1" @selected($selectedSasaranStrategis === 'T1')>T1 — Kebijakan Berbasis Data</option>
            <option value="T2" @selected($selectedSasaranStrategis === 'T2')>T2 — Sistem Statistik Nasional</option>
            <option value="T3" @selected($selectedSasaranStrategis === 'T3')>T3 — Tata Kelola BPS</option>
        </select>
        @error('sasaran_strategis')
            <p id="{{ $formId }}-sasaran-strategis-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-1.5">
        <label for="{{ $formId }}-target" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Target IKSS</label>
        <input id="{{ $formId }}-target" name="target" type="text" maxlength="100"
            value="{{ old('target', $ikssMaster->target) }}"
            @if ($errors->has('target')) aria-invalid="true" aria-describedby="{{ $formId }}-target-error" @endif
            class="min-h-12 rounded-xl border border-slate-300 bg-white px-3 text-base text-slate-800 shadow-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        @error('target')
            <p id="{{ $formId }}-target-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-1.5 sm:col-span-2">
        <label for="{{ $formId }}-sasaran-kegiatan" class="text-sm font-semibold text-slate-700 dark:text-slate-200">
            Sasaran Kegiatan <span class="text-orange-600" aria-hidden="true">*</span>
        </label>
        <textarea id="{{ $formId }}-sasaran-kegiatan" name="sasaran_kegiatan" rows="2" required
            @if ($errors->has('sasaran_kegiatan')) aria-invalid="true" aria-describedby="{{ $formId }}-sasaran-kegiatan-error" @endif
            class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-800 shadow-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('sasaran_kegiatan', $ikssMaster->sasaran_kegiatan) }}</textarea>
        @error('sasaran_kegiatan')
            <p id="{{ $formId }}-sasaran-kegiatan-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-1.5 sm:col-span-2">
        <label for="{{ $formId }}-indikator-kinerja" class="text-sm font-semibold text-slate-700 dark:text-slate-200">
            Indikator Kinerja <span class="text-orange-600" aria-hidden="true">*</span>
        </label>
        <textarea id="{{ $formId }}-indikator-kinerja" name="indikator_kinerja" rows="2" required
            @if ($errors->has('indikator_kinerja')) aria-invalid="true" aria-describedby="{{ $formId }}-indikator-kinerja-error" @endif
            class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-800 shadow-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('indikator_kinerja', $ikssMaster->indikator_kinerja) }}</textarea>
        @error('indikator_kinerja')
            <p id="{{ $formId }}-indikator-kinerja-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>
</fieldset>
