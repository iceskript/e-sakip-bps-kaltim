<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - e-SAKIP BPS Kaltim</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">

    <script>
        const savedTheme = localStorage.getItem('e-sakip-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', savedTheme === 'dark' || (savedTheme === null && prefersDark));
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="expect" href="#main-content" blocking="render">
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800 transition-colors duration-300" x-data="{
    isDark: document.documentElement.classList.contains('dark'),
    profileMenuOpen: false,
    passwordModalOpen: @js($errors->has('current_password') || $errors->has('new_password')),
    sidebarOpen: true,
    lakinSubmenuOpen: @js(request()->routeIs('lakin.*')),
    initTheme() {
        if (localStorage.getItem('e-sakip-theme') !== null) {
            return;
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', ({ matches }) => {
            if (localStorage.getItem('e-sakip-theme') === null) {
                this.isDark = matches;
                document.documentElement.classList.toggle('dark', matches);
            }
        });
    },
    toggleTheme() {
        this.isDark = !this.isDark;
        document.documentElement.classList.toggle('dark', this.isDark);
        localStorage.setItem('e-sakip-theme', this.isDark ? 'dark' : 'light');
    },
    setTheme(theme) {
        this.isDark = theme === 'dark';
        document.documentElement.classList.toggle('dark', this.isDark);
        localStorage.setItem('e-sakip-theme', theme);
    }
}"
    x-init="initTheme()">
    <section class="relative flex min-h-screen flex-col lg:flex-row">

        <!-- SIDEBAR -->
        <aside id="primary-sidebar"
            class="relative z-40 flex h-auto w-full shrink-0 flex-col border-b border-slate-200 bg-white shadow-sm lg:fixed lg:inset-y-0 lg:left-0 lg:h-screen lg:w-[280px] lg:border-b-0 lg:border-r lg:shadow-none"
            aria-label="Sidebar Navigasi">

            <!-- Header Sidebar -->
            <header class="flex h-16 shrink-0 items-center border-b border-slate-100 px-4 lg:h-[88px] lg:px-6">
                <section class="flex items-center overflow-hidden">
                    <figure class="flex-shrink-0">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo BPS" class="w-10 h-10 object-contain">
                    </figure>

                    <section x-show="sidebarOpen" class="ml-4 flex flex-col justify-center">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight leading-none">e-SAKIP</h1>
                        <p
                            class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1.5 leading-tight whitespace-nowrap">
                            BPS Provinsi Kaltim
                        </p>
                    </section>
                </section>

            </header>

            <!-- Navigasi Menu -->
            <nav id="primary-navigation" class="flex flex-1 flex-row gap-1 overflow-x-auto overflow-y-hidden px-3 py-2 lg:flex-col lg:overflow-x-hidden lg:overflow-y-auto lg:py-4"
                aria-label="Menu Navigasi Utama">

                <!-- Beranda -->
                <a href="{{ route('dashboard') }}"
                    @class([
                        'flex items-center px-3 py-3 rounded-lg transition-all',
                        'bg-blue-600 text-white shadow-sm' => request()->routeIs('dashboard'),
                        'text-slate-600 hover:bg-blue-600 hover:text-white' => ! request()->routeIs('dashboard'),
                    ])
                    @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-bold text-sm">Beranda</span>
                        <span class="text-[10px] text-blue-200">Halaman Utama</span>
                    </span>
                </a>

                <!-- Perencanaan -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap text-slate-400">
                    Perencanaan</h2>
                <a href="{{ route('dokumens.index', ['menuKey' => 'renstra']) }}"
                    @class([
                        'flex items-center px-3 py-2.5 rounded-lg transition-colors group',
                        'bg-blue-600 text-white shadow-sm' => request()->routeIs('dokumens.index') && request()->route('menuKey') === 'renstra',
                        'text-slate-600 hover:bg-blue-600 hover:text-white' => ! (request()->routeIs('dokumens.index') && request()->route('menuKey') === 'renstra'),
                    ])
                    @if (request()->routeIs('dokumens.index') && request()->route('menuKey') === 'renstra') aria-current="page" @endif>
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 22a10 10 0 100-20 10 10 0 000 20z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 18a6 6 0 100-12 6 6 0 000 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 14a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Renstra</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Rencana Strategis</span>
                    </span>
                </a>
                <a href="{{ route('dokumens.index', ['menuKey' => 'perjanjian_kinerja']) }}"
                    @class([
                        'flex items-center px-3 py-2.5 rounded-lg transition-colors group',
                        'bg-blue-600 text-white shadow-sm' => request()->routeIs('dokumens.index') && request()->route('menuKey') === 'perjanjian_kinerja',
                        'text-slate-600 hover:bg-blue-600 hover:text-white' => ! (request()->routeIs('dokumens.index') && request()->route('menuKey') === 'perjanjian_kinerja'),
                    ])
                    @if (request()->routeIs('dokumens.index') && request()->route('menuKey') === 'perjanjian_kinerja') aria-current="page" @endif>
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12l-3 3m0 0l-3-3m3 3V8" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Perjanjian
                            Kinerja</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Dokumen Komitmen</span>
                    </span>
                </a>

                <!-- Pengukuran -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap text-slate-400">
                    Pengukuran</h2>
                <a href="{{ route('monitoring.index') }}"
                    @class([
                        'flex items-center px-3 py-2.5 rounded-lg transition-colors group',
                        'bg-blue-600 text-white shadow-sm' => request()->routeIs('monitoring.index'),
                        'text-slate-600 hover:bg-blue-600 hover:text-white' => ! request()->routeIs('monitoring.index'),
                    ])
                    @if (request()->routeIs('monitoring.index')) aria-current="page" @endif>
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Monitoring Capaian
                            Kinerja</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Triwulan</span>
                    </span>
                </a>
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Monitoring Capaian
                            Renstra</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Capaian Akhir Renstra</span>
                    </span>
                </a>
                <a href="{{ route('capaian-kinerja.index') }}"
                    @class([
                        'flex items-center px-3 py-2.5 rounded-lg transition-colors group',
                        'bg-blue-600 text-white shadow-sm' => request()->routeIs('capaian-kinerja.index', 'capaian-kinerja.update'),
                        'text-slate-600 hover:bg-blue-600 hover:text-white' => ! request()->routeIs('capaian-kinerja.index', 'capaian-kinerja.update'),
                    ])
                    @if (request()->routeIs('capaian-kinerja.index', 'capaian-kinerja.update')) aria-current="page" @endif>
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Entry Capaian
                            Kinerja</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Input capaian per Triwulan</span>
                    </span>
                </a>
                <a href="{{ route('notula.index') }}"
                    @class([
                        'flex items-center px-3 py-2.5 rounded-lg transition-colors group',
                        'bg-blue-600 text-white shadow-sm' => request()->routeIs('notula.*'),
                        'text-slate-600 hover:bg-blue-600 hover:text-white' => ! request()->routeIs('notula.*'),
                    ])
                    @if (request()->routeIs('notula.*')) aria-current="page" @endif>
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Generate
                            Notula</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Notula Monitoring
                            Kinerja</span>
                    </span>
                </a>

                <!-- Pelaporan -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap text-slate-400">
                    Pelaporan</h2>
                <div class="lakin-menu relative mb-1 shrink-0">
                    <button type="button" @click="lakinSubmenuOpen = !lakinSubmenuOpen"
                        aria-controls="lakin-submenu" :aria-expanded="lakinSubmenuOpen.toString()"
                        @class([
                            'group flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-left transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600',
                            'bg-blue-600 text-white shadow-sm' => request()->routeIs('lakin.*'),
                            'text-slate-600 hover:bg-blue-600 hover:text-white' => ! request()->routeIs('lakin.*'),
                        ])>
                        <span class="flex items-center">
                            <svg class="mr-3 h-5 w-5 shrink-0 text-slate-400 group-hover:text-white" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                                <span class="text-sm font-semibold">LAKIN</span>
                                <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Laporan Akuntabilitas</span>
                            </span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
                            :class="{ 'rotate-180': lakinSubmenuOpen }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <ul id="lakin-submenu" x-show="lakinSubmenuOpen" x-transition
                        class="lakin-submenu absolute left-0 top-full z-50 mt-1 min-w-48 space-y-1 rounded-lg border border-slate-200 bg-white p-2 shadow-lg dark:border-slate-700 dark:bg-slate-800"
                        aria-label="Menu LAKIN">
                        @foreach (['Draft', 'Final'] as $lakinType)
                            <li>
                                <a href="{{ route('lakin.index', ['tipe' => $lakinType]) }}"
                                    @class([
                                        'block rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                        'bg-slate-100 text-slate-900 dark:bg-slate-700 dark:text-slate-100' => request()->query('tipe', 'Draft') === $lakinType,
                                        'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white' => request()->query('tipe', 'Draft') !== $lakinType,
                                    ])
                                    @if (request()->routeIs('lakin.index') && request()->query('tipe', 'Draft') === $lakinType) aria-current="page" @endif>
                                    {{ $lakinType }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Evaluasi -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap text-slate-400">
                    Evaluasi</h2>
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Permintaan
                            Dokumen</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Permindak by Tahun</span>
                    </span>
                </a>

                <!-- Aplikasi Monitoring -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap text-slate-400">
                    Aplikasi Monitoring</h2>
                <a href="https://esr.menpan.go.id/" target="_blank" rel="noopener noreferrer"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">ESR</span>
                        <span class="sr-only"> (opens in a new tab)</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Evaluasi Sistem
                            Monitoring</span>
                    </span>
                </a>
                <a href="https://sinergi.web.bps.go.id/" target="_blank" rel="noopener noreferrer"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">SINERGI</span>
                        <span class="sr-only"> (opens in a new tab)</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Kolaborasi Unit</span>
                    </span>
                </a>

                <!-- Referensi -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap text-slate-400">
                    Referensi</h2>
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Referensi &
                            Panduan</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Referensi Sakip</span>
                    </span>
                </a>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center rounded-lg px-3 py-2.5 text-slate-600 transition-colors group hover:bg-blue-600 hover:text-white">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0 text-slate-400 group-hover:text-white" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-1v6m3-3h-6" />
                        </svg>
                        <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                            <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Kelola akun</span>
                            <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Akses pengguna</span>
                        </span>
                    </a>
                @endif

                <!-- FOOTER SIDEBAR -->
                <footer class="hidden shrink-0 border-t border-slate-100 px-3 py-4 lg:block">
                    <div class="flex items-center gap-3 px-2">
                        <!-- Logo BPS Kecil -->
                        <figure class="flex-shrink-0">
                            <img src="{{ asset('images/logo.webp') }}" alt="Logo BPS"
                                class="w-6 h-6 object-contain opacity-70">
                        </figure>
                        <div x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                            <p class="text-[10px] font-bold text-slate-500">BPS Prov. Kaltim</p>
                            <p class="text-[9px] text-slate-400">e-SAKIP v1.0.0</p>
                        </div>
                    </div>
                </footer>
            </nav>
        </aside>

        <!-- KONTEN UTAMA -->
        <main id="main-content"
            class="flex min-h-screen min-w-0 flex-1 flex-col bg-slate-50 transition-colors duration-300 lg:ml-[280px]">

            <!-- Header Atas -->
            <header
                class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-3 sm:px-8 z-10 shrink-0 transition-colors duration-300 dark:border-slate-800 dark:bg-slate-900">
                <div class="flex min-w-0 items-center gap-2 sm:gap-4">
                    <h1 class="text-lg font-semibold text-slate-800 truncate min-w-0 dark:text-slate-100">@yield('title', 'Beranda')</h1>
                </div>

                <!-- Profil User -->
                <section class="shrink-0" aria-label="Profil pengguna">
                    <button type="button" @click="profileMenuOpen = !profileMenuOpen"
                        class="flex max-w-[min(60vw,18rem)] items-center gap-2 rounded-lg px-2 py-1.5 text-left transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 dark:hover:bg-slate-800"
                        aria-label="Menu akun" aria-haspopup="menu" :aria-expanded="profileMenuOpen.toString()">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-blue-200 bg-blue-50 text-sm font-semibold text-blue-800 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-200"
                            aria-hidden="true">
                            {{ Illuminate\Support\Str::upper(Illuminate\Support\Str::substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="min-w-0 sm:block">
                            <span class="block text-xs font-medium text-slate-800 sm:hidden dark:text-slate-100">Akun</span>
                            <span class="hidden max-w-40 truncate text-sm font-semibold text-slate-800 dark:text-slate-100 sm:block">{{ auth()->user()->name }}</span>
                            <span class="hidden max-w-40 truncate text-xs text-slate-500 dark:text-slate-400 sm:block">{{ auth()->user()->email }}</span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-slate-500 transition-transform" :class="{ 'rotate-180': profileMenuOpen }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <template x-teleport="body">
                        <div x-cloak x-show="profileMenuOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="translate-y-1 scale-95 opacity-0"
                            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                            x-transition:leave-end="translate-y-1 scale-95 opacity-0"
                            @click.outside="profileMenuOpen = false"
                            @keydown.escape.window="profileMenuOpen = false; passwordModalOpen = false"
                            class="fixed right-3 top-[4.25rem] z-50 w-72 origin-top-right rounded-xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900 sm:right-6"
                            role="menu" aria-label="Menu akun">
                            <div class="border-b border-slate-100 px-3 py-2 dark:border-slate-800 sm:hidden">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="px-3 py-3">
                                <div class="relative grid grid-cols-2 rounded-full bg-slate-100 p-1 dark:bg-slate-800">
                                    <span aria-hidden="true"
                                        class="theme-slider-indicator absolute inset-y-1 left-1 rounded-full bg-white shadow-sm transition-transform duration-300 ease-out dark:bg-slate-700"
                                        :class="isDark ? 'translate-x-full' : 'translate-x-0'"></span>
                                    <button type="button" @click="setTheme('light')"
                                        class="relative z-10 flex min-h-9 items-center justify-center rounded-full text-slate-600 transition-colors dark:text-slate-300"
                                        :class="!isDark ? 'text-amber-600 dark:text-amber-300' : ''"
                                        :aria-pressed="(!isDark).toString()" aria-label="Tema terang"
                                        role="menuitem">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="12" r="4" stroke-width="1.8" />
                                            <path stroke-linecap="round" stroke-width="1.8" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="setTheme('dark')"
                                        class="relative z-10 flex min-h-9 items-center justify-center rounded-full text-slate-600 transition-colors dark:text-slate-300"
                                        :class="isDark ? 'text-blue-700 dark:text-blue-200' : ''"
                                        :aria-pressed="isDark.toString()" aria-label="Tema gelap"
                                        role="menuitem">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5a8.6 8.6 0 1 0 12 12Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <button type="button" @click="profileMenuOpen = false; passwordModalOpen = true"
                                class="flex min-h-10 w-full items-center gap-2 rounded-lg px-3 text-left text-sm text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 dark:text-slate-200 dark:hover:bg-slate-800"
                                role="menuitem">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2Zm12-11V7a6 6 0 0 0-12 0v4" />
                                </svg>
                                Pengaturan akun
                            </button>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-1 dark:border-slate-800">
                                @csrf
                                <button type="submit"
                                    class="flex min-h-10 w-full items-center gap-2 rounded-lg px-3 text-left text-sm font-medium text-rose-700 transition hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600 dark:text-rose-300 dark:hover:bg-rose-950/30"
                                    role="menuitem">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-4-4 4-5m0 0-4-5m4 5H3" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </template>

                    <template x-teleport="body">
                        <div x-cloak x-show="passwordModalOpen" x-transition.opacity
                                @click.self="passwordModalOpen = false" @keydown.escape.window="passwordModalOpen = false"
                                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4"
                                role="presentation">
                                <section x-show="passwordModalOpen" x-transition
                                    class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-5 shadow-2xl dark:border-slate-700 dark:bg-slate-900 sm:p-6"
                                    role="dialog" aria-modal="true" aria-labelledby="password-modal-title">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h2 id="password-modal-title" class="text-lg font-semibold text-slate-900 dark:text-white">Ganti password</h2>
                                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Konfirmasi password saat ini untuk melanjutkan.</p>
                                        </div>
                                        <button type="button" @click="passwordModalOpen = false"
                                            class="rounded-md p-1.5 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"
                                            aria-label="Tutup">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 6 12 12M18 6 6 18" />
                                            </svg>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('account.password.update') }}" class="mt-5 space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label for="current_password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Password saat ini</label>
                                            <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                            @error('current_password')
                                                <p class="mt-1.5 text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="new_password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Password baru</label>
                                            <input id="new_password" name="new_password" type="password" required minlength="12" autocomplete="new-password"
                                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                            @error('new_password')
                                                <p class="mt-1.5 text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="new_password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Ulangi password baru</label>
                                            <input id="new_password_confirmation" name="new_password_confirmation" type="password" required minlength="12" autocomplete="new-password"
                                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                        </div>
                                        <div class="flex justify-end gap-2 pt-1">
                                            <button type="button" @click="passwordModalOpen = false"
                                                class="min-h-10 rounded-lg border border-slate-300 px-3.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="min-h-10 rounded-lg bg-[#123b50] px-4 text-sm font-semibold text-white hover:bg-[#0e3041]">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </section>
                        </div>
                    </template>
                </section>
            </header>

            <!-- Konten Dinamis -->
            <article class="flex-1 overflow-y-auto p-4 sm:p-8 transition-colors duration-300">
                @yield('content')
            </article>
        </main>
    </section>
</body>

</html>
