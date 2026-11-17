<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-SAKIP BPS Kaltim</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800" x-data="{ sidebarOpen: true }">
    
    <!-- Menggunakan <section> untuk layout utama flexbox -->
    <section class="flex h-screen overflow-hidden">
        
        <!-- SEMANTIC: Aside untuk Sidebar Navigasi -->
        <aside class="bg-white border-r border-slate-200 flex flex-col transition-all duration-300 z-20 shadow-[2px_0_10px_rgba(0,0,0,0.02)]" 
               :class="sidebarOpen ? 'w-[280px]' : 'w-20'"
               aria-label="Sidebar Navigasi">
            
            <!-- SEMANTIC: Header untuk Logo & Branding -->
            <header class="h-[88px] flex items-center px-6 border-b border-slate-100">
                <!-- SEMANTIC: Figure untuk Gambar Logo -->
                <figure class="flex-shrink-0">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo BPS Provinsi Kalimantan Timur" class="w-10 h-10 object-contain">
                </figure>
                
                <section x-show="sidebarOpen" class="ml-4 flex flex-col justify-center">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight leading-none">e-SAKIP</h1>
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mt-1.5 leading-tight">
                        BPS Provinsi Kaltim
                    </p>
                </section>
            </header>

            <!-- SEMANTIC: Nav untuk Menu -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1" aria-label="Menu Navigasi Utama">
                
                <!-- MENU AKTIF: Beranda -->
                <a href="{{ url('/dashboard') }}" class="flex items-center px-3 py-3 bg-blue-600 text-white rounded-lg shadow-sm transition-all" aria-current="page">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="sidebarOpen" class="flex flex-col">
                        <span class="font-bold text-sm">Beranda</span>
                        <span class="text-[10px] text-blue-200">Halaman Utama</span>
                    </span>
                </a>

                <!-- KATEGORI: PERENCANAAN -->
                <h2 x-show="sidebarOpen" class="px-3 pt-6 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Perencanaan</h2>
                
                <!-- MENU BIASA -->
                <a href="#" class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-slate-50 rounded-lg transition-colors group">
                    <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="sidebarOpen" class="flex flex-col">
                        <span class="font-semibold text-sm text-slate-700 group-hover:text-slate-900">Renstra</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-slate-500">Rencana Strategis</span>
                    </span>
                </a>

                <!-- Tambahkan menu lainnya dengan format yang sama... -->
            </nav>
        </aside>

        <!-- SEMANTIC: Main untuk Konten Utama -->
        <main class="flex-1 flex flex-col overflow-hidden bg-slate-50">
            
            <!-- SEMANTIC: Header Atas -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10">
                <nav class="flex items-center gap-4" aria-label="Navigasi Atas">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-slate-600" aria-label="Buka atau tutup menu sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-slate-800">@yield('title', 'Beranda')</h1>
                </nav>
                
                <!-- Profil User -->
                <section class="flex items-center gap-4" aria-label="Profil Pengguna">
                    <section class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800">Administrator</p>
                        <p class="text-xs text-slate-500">BPS Kaltim</p>
                    </section>
                    <figure class="w-9 h-9 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center font-bold border border-slate-200" aria-hidden="true">
                        A
                    </figure>
                </section>
            </header>

            <!-- AREA KONTEN DINAMIS -->
            <article class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </article>
        </main>
    </section>
</body>
</html>