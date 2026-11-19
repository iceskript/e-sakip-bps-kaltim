<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-SAKIP BPS Kaltim</title>

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
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800 transition-colors duration-300" x-data="{
    isDark: document.documentElement.classList.contains('dark'),
    sidebarOpen: window.innerWidth >= 1024,
    isDesktop: window.innerWidth >= 1024,
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
    syncSidebarToViewport() {
        const isDesktop = window.innerWidth >= 1024;
        if (isDesktop !== this.isDesktop) {
            this.isDesktop = isDesktop;
            this.sidebarOpen = isDesktop;
        }
    }
}"
    x-init="initTheme()" @resize.window="syncSidebarToViewport()">
    <section class="flex h-screen overflow-hidden relative">

        <div x-show="sidebarOpen" x-transition.opacity.duration.300ms @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/60 z-30 lg:hidden" aria-hidden="true" style="display: none;"></div>

        <!-- SIDEBAR -->
        <aside id="primary-sidebar"
            class="bg-white border-r border-slate-200 flex flex-col transition-all duration-300 z-40 shadow-2xl lg:shadow-none
                   fixed lg:relative inset-y-0 left-0 h-full"
            :class="sidebarOpen ? 'translate-x-0 w-[280px]' : '-translate-x-full lg:translate-x-0 lg:w-20'"
            aria-label="Sidebar Navigasi">

            <!-- Header Sidebar -->
            <header class="h-[88px] flex items-center px-6 border-b border-slate-100 justify-between shrink-0">
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

                <button @click="sidebarOpen = false"
                    class="lg:hidden text-slate-400 hover:text-red-500 p-2 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <!-- Navigasi Menu -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1" aria-label="Menu Navigasi Utama"
                @click="if (!isDesktop && $event.target.closest('a')) { sidebarOpen = false }">

                <!-- Beranda -->
                <a href="{{ url('/dashboard') }}"
                    class="flex items-center px-3 py-3 bg-blue-600 text-white rounded-lg shadow-sm transition-all"
                    aria-current="page">
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
                    class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">
                    Perencanaan</h2>
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
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
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
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
                    class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">
                    Pengukuran</h2>
                <a href="{{ url('/ikss') }}"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Monitoring
                            Capaian</span>
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
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">Entry Capaian
                            Kinerja</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Input IKU Per
                            Triwulan</span>
                    </span>
                </a>
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
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
                    class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">
                    Pelaporan</h2>
                <details class="group mb-1">
                    <summary
                        class="flex items-center justify-between px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors cursor-pointer list-none [&::-webkit-details-marker]:hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 group">
                        <section class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span x-show="sidebarOpen" class="flex flex-col text-left whitespace-nowrap">
                                <span class="font-semibold text-sm text-slate-700 group-hover:text-white">LAKIN</span>
                                <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Laporan
                                    Akuntabilitas</span>
                            </span>
                        </section>
                        <svg x-show="sidebarOpen"
                            class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180 group-hover:text-white"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>
                    <section class="mt-1 space-y-1 pl-11 pr-2">
                        <a href="#"
                            class="block px-3 py-2 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-colors">Draft</a>
                        <a href="#"
                            class="block px-3 py-2 text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-slate-50 rounded-lg transition-colors">Final</a>
                    </section>
                </details>

                <!-- Evaluasi -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">
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
                    class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">
                    Aplikasi Monitoring</h2>
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-white">ESR</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Evaluasi Sistem
                            Monitoring</span>
                    </span>
                </a>
                <a href="#"
                    class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-blue-600 hover:text-white rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-white flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="sidebarOpen" class="flex flex-col whitespace-nowrap">
                        <span
                            class="font-semibold text-sm tex
                        t-slate-700 group-hover:text-white">SINERGI</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-200">Kolaborasi Unit</span>
                    </span>
                </a>

                <!-- Referensi -->
                <h2 x-show="sidebarOpen"
                    class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">
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

                <!-- FOOTER SIDEBAR -->
                <footer class="px-3 py-4 border-t border-slate-100 shrink-0">
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
            class="flex-1 flex flex-col overflow-hidden bg-slate-50 w-full min-w-0 transition-colors duration-300">

            <!-- Header Atas -->
            <header
                class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-3 sm:px-8 z-10 shrink-0 transition-colors duration-300">
                <nav class="flex items-center gap-2 sm:gap-4 min-w-0" aria-label="Navigasi Atas">
                    <!-- Tombol Hamburger -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-slate-500 hover:text-slate-800 p-2 -ml-2 rounded-lg hover:bg-slate-100 transition-colors shrink-0 dark:hover:bg-slate-800 dark:hover:text-slate-100"
                        aria-label="Toggle Sidebar" aria-controls="primary-sidebar"
                        :aria-expanded="sidebarOpen.toString()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-slate-800 truncate min-w-0">@yield('title', 'Beranda')</h1>
                </nav>

                <!-- Profil User -->
                <section class="flex items-center gap-2 sm:gap-4 shrink-0" aria-label="Profil Pengguna">
                    <button @click="toggleTheme()"
                        class="w-9 h-9 flex items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 hover:text-blue-600 transition-colors dark:text-slate-300 dark:hover:bg-slate-800"
                        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                        :aria-pressed="isDark.toString()" title="Change color theme">
                        <svg x-cloak x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                        </svg>
                        <svg x-cloak x-show="isDark" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="4" stroke-width="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" />
                        </svg>
                    </button>
                    <section class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800">Administrator</p>
                        <p class="text-xs text-slate-500">BPS Kaltim</p>
                    </section>
                    <figure
                        class="w-9 h-9 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center font-bold border border-slate-200"
                        aria-hidden="true">
                        A
                    </figure>
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
