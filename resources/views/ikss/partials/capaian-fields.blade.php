<input type="hidden" name="_dialog" value="{{ $dialogKey }}">
<input type="hidden" name="tahun" value="{{ $tahun }}">
<input type="hidden" name="triwulan" value="{{ $triwulan }}">

<p class="text-sm text-slate-500 dark:text-slate-400">
    Status indikator akan mengikuti kelengkapan lima nilai capaian.
</p>

<fieldset class="grid gap-4 sm:grid-cols-2">
    <legend class="sr-only">Nilai capaian triwulanan</legend>
    @foreach ([
        'target_pk' => 'Target PK',
        'target_tw' => 'Target Triwulan',
        'realisasi' => 'Realisasi',
        'capaian_persen' => 'Capaian (%)',
        'capaian_tw_sebelumnya' => 'Capaian TW Sebelumnya',
    ] as $field => $label)
        <div class="grid gap-1.5">
            <label for="{{ $formId }}-{{ $field }}" class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $label }}</label>
            <input id="{{ $formId }}-{{ $field }}" name="{{ $field }}" type="number" inputmode="decimal" min="0" max="99999999.99" step="0.01"
                @if ($loop->first) autofocus @endif
                value="{{ old($field, $capaian?->{$field}) }}"
                @if ($errors->has($field)) aria-invalid="true" aria-describedby="{{ $formId }}-{{ $field }}-error" @endif
                class="min-h-12 rounded-xl border border-slate-300 bg-white px-3 text-base text-slate-800 shadow-sm transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-blue-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            @error($field)
                <p id="{{ $formId }}-{{ $field }}-error" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>
            @enderror
        </div>
    @endforeach
</fieldset>
