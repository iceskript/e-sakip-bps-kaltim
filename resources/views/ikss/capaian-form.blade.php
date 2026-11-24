@extends('layouts.app')

@section('title', 'Capaian Kinerja')

@section('content')
    <header class="mb-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
            {{ $capaian ? 'Edit Capaian Kinerja' : 'Input Capaian Kinerja' }}
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            {{ $ikssMaster->kode_indikator }} · {{ $ikssMaster->indikator_kinerja }}
        </p>
    </header>

    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-200">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="max-w-4xl rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <form action="{{ route('ikss.capaian.update', $ikssMaster) }}" method="POST" aria-labelledby="capaian-form-heading">
            @csrf
            @method('PUT')
            <h3 id="capaian-form-heading" class="mb-2 text-base font-semibold text-slate-900 dark:text-slate-100">
                {{ $triwulan }} — {{ $tahun }}
            </h3>
            @include('ikss.partials.capaian-fields', [
                'formId' => 'capaian-form',
                'dialogKey' => 'capaian-form',
            ])

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit"
                    class="min-h-11 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                    Simpan Capaian
                </button>
                <a href="{{ route('ikss.index', ['tahun' => $tahun, 'triwulan' => $triwulan]) }}"
                    class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                    Kembali
                </a>
            </div>
        </form>

        @if ($capaian)
            <div x-data class="mt-3 flex justify-end">
                <button type="button" @click="$refs.deleteDialog.showModal()"
                    class="min-h-11 rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950">
                    Hapus Capaian
                </button>
                <dialog x-ref="deleteDialog" aria-labelledby="delete-capaian-title"
                    class="m-auto w-[90vw] max-w-lg rounded-xl border border-slate-200 bg-white p-6 text-slate-800 shadow-2xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                    <h2 id="delete-capaian-title" class="text-lg font-semibold">Hapus data capaian?</h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        Data capaian {{ $ikssMaster->kode_indikator }} untuk {{ $triwulan }} {{ $tahun }} akan dihapus.
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <form method="dialog">
                            <button type="submit" autofocus
                                class="min-h-11 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium dark:border-slate-600">
                                Batal
                            </button>
                        </form>
                        <form action="{{ route('capaian-kinerja.destroy', $capaian) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="min-h-11 rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                                Hapus Capaian
                            </button>
                        </form>
                    </div>
                </dialog>
            </div>
        @endif
    </section>
@endsection
