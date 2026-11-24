@extends('layouts.app')

@php
    $isEditing = $ikssMaster->exists;
    $formTitle = $isEditing ? 'Edit Indikator IKSS' : 'Tambah Indikator IKSS';
@endphp

@section('title', $formTitle)

@section('content')
    <header class="mb-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $formTitle }}</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Perbarui data master indikator kinerja.</p>
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
        <form action="{{ $isEditing ? route('ikss.update', $ikssMaster) : route('ikss.store') }}" method="POST"
            aria-labelledby="ikss-form-heading">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif
            @include('ikss.partials.ikss-fields', [
                'formId' => 'ikss-form',
                'dialogKey' => 'ikss-form',
            ])

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit"
                    class="min-h-11 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">
                    {{ $isEditing ? 'Simpan Perubahan' : 'Simpan IKSS' }}
                </button>
                <a href="{{ route('ikss.index', ['tahun' => $tahun, 'triwulan' => $triwulan]) }}"
                    class="inline-flex min-h-11 items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                    Batal
                </a>
            </div>
        </form>
    </section>
@endsection
