@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<section class="max-w-7xl mx-auto space-y-6">
    
    <!-- BANNER HERO -->
    <article class="bg-white rounded-xl border border-slate-200 p-8 shadow-sm flex items-center justify-between">
        <section class="max-w-2xl">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">
                Selamat Datang di <span class="text-blue-700">e-SAKIP</span> BPS Kaltim
            </h2>
            <p class="text-slate-500 text-sm leading-relaxed">
                Sistem Manajemen SAKIP Terintegrasi BPS Provinsi Kalimantan Timur. Silakan kelola data kinerja, monitoring capaian, dan pelaporan dengan mudah.
            </p>
        </section>
        <!-- Aksen Gambar di Kanan -->
        <figure class="hidden lg:block w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center">
             <img src="{{ asset('images/logo.webp') }}" class="w-12 h-12 opacity-50" alt="Logo BPS">
        </figure>
    </article>

    <!-- STATISTIK SECTION -->
    <section aria-labelledby="ringkasan-status">
        <h2 id="ringkasan-status" class="text-lg font-semibold text-slate-800 mb-4">Ringkasan Status</h2>
        
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="flex justify-between items-start mb-4">
                    <h3 class="text-sm font-semibold text-slate-500">Belum Mulai</h3>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                </header>
                <p class="text-3xl font-bold text-slate-800 mb-1">0</p>
                <footer class="text-xs text-slate-400">Indikator Kinerja</footer>
            </article>

            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="flex justify-between items-start mb-4">
                    <h3 class="text-sm font-semibold text-slate-500">Sedang Berjalan</h3>
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500" aria-hidden="true"></span>
                </header>
                <p class="text-3xl font-bold text-blue-700 mb-1">0</p>
                <footer class="text-xs text-slate-400">Indikator Kinerja</footer>
            </article>

            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="flex justify-between items-start mb-4">
                    <h3 class="text-sm font-semibold text-slate-500">Selesai</h3>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                </header>
                <p class="text-3xl font-bold text-emerald-600 mb-1">6</p>
                <footer class="text-xs text-slate-400">Indikator Kinerja</footer>
            </article>

            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="flex justify-between items-start mb-4">
                    <h3 class="text-sm font-semibold text-slate-500">Deadline 30 Hari</h3>
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500" aria-hidden="true"></span>
                </header>
                <p class="text-3xl font-bold text-orange-600 mb-1">0</p>
                <footer class="text-xs text-slate-400">Jadwal Mendatang</footer>
            </article>

        </section>
    </section>

    <!-- AREA KALENDER & PENGINGAT -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <article class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6" aria-labelledby="kalender-title">
            <header class="mb-4">
                <h2 id="kalender-title" class="text-lg font-semibold text-slate-800">Kalender Jadwal SAKIP</h2>
            </header>
            <section class="h-64 flex flex-col items-center justify-center bg-slate-50 border border-dashed border-slate-300 rounded-lg text-slate-400">
                <p class="text-sm">Area Kalender (Bisa diisi FullCalendar)</p>
            </section>
        </article>
        
        <article class="bg-white rounded-xl border border-slate-200 shadow-sm p-6" aria-labelledby="pengingat-title">
            <header class="flex justify-between items-center mb-4">
                 <h2 id="pengingat-title" class="text-lg font-semibold text-slate-800">Deadline Mendatang</h2>
                 <span class="text-[10px] text-slate-400">(30 hari)</span>
            </header>
            
            <section class="flex flex-col items-center justify-center h-48 text-center text-slate-500">
                <svg class="w-6 h-6 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs text-emerald-600 font-medium">Tidak ada deadline dalam 30 hari ke depan</p>
            </section>
        </article>
    </section>

</section>
@endsection