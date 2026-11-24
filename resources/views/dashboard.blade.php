@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<section class="max-w-7xl mx-auto space-y-6"
    x-data="{
        reminderPopupOpen: false,
        activityFormOpen: @js($errors->any()),
        activityStartDate: @js(old('tanggal_mulai', now()->toDateString())),
        activityEndDate: @js(old('tanggal_selesai', now()->toDateString())),
        activityDetailsOpen: false,
        deleteConfirmationOpen: false,
        selectedActivity: {},
        openActivityForm(date = null) {
            if (date) {
                this.activityStartDate = date;
                this.activityEndDate = date;
            }

            this.activityFormOpen = true;
        },
        openActivityDetails(activity) {
            this.selectedActivity = activity;
            this.activityDetailsOpen = true;
        },
        openDeleteConfirmation(activity = null) {
            if (activity) {
                this.selectedActivity = activity;
            }

            this.activityDetailsOpen = false;
            this.deleteConfirmationOpen = true;
        },
        init() {
            const hasSoonActivities = @js($soonActivities->isNotEmpty());
            const reminderKey = 'sakip-activity-reminder-{{ now()->toDateString() }}';

            if (hasSoonActivities && !sessionStorage.getItem(reminderKey)) {
                this.reminderPopupOpen = true;
                sessionStorage.setItem(reminderKey, 'shown');
            }
        }
    }">

    @if (session('status'))
        <div role="status"
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <template x-teleport="body">
        <aside x-cloak x-show="reminderPopupOpen" x-transition
        class="fixed right-4 top-20 z-50 w-[calc(100%-2rem)] max-w-sm rounded-xl border border-amber-200 bg-white p-4 shadow-xl dark:border-amber-900 dark:bg-slate-900"
        role="status" aria-labelledby="activity-reminder-title">
        <header class="mb-2 flex items-start justify-between gap-3">
            <div>
                <h2 id="activity-reminder-title" class="font-semibold text-slate-900 dark:text-slate-100">
                    Agenda segera
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Kegiatan yang dimulai dalam 7 hari.</p>
            </div>
            <button type="button" @click="reminderPopupOpen = false"
                class="rounded p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                aria-label="Tutup pengingat">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </header>
        <ul class="space-y-1">
            @foreach ($soonActivities->take(3) as $activity)
                <li>
                    <button type="button"
                        data-activity="{{ json_encode([
                            'id' => $activity->id,
                            'judul' => $activity->judul,
                            'kategori' => $activity->kategori,
                            'tanggalMulai' => $activity->tanggal_mulai->locale('id')->translatedFormat('d M Y'),
                            'tanggalSelesai' => $activity->tanggal_selesai->locale('id')->translatedFormat('d M Y'),
                            'deskripsi' => $activity->deskripsi,
                            'selesai' => $activity->selesai,
                        ]) }}"
                        @click="openActivityDetails(JSON.parse($event.currentTarget.dataset.activity)); reminderPopupOpen = false"
                        class="flex w-full items-center justify-between gap-3 rounded-lg px-2 py-2 text-left text-sm hover:bg-amber-50 dark:hover:bg-slate-800">
                        <span class="truncate font-medium text-slate-700 dark:text-slate-200">{{ $activity->judul }}</span>
                        <span class="shrink-0 text-xs text-slate-500 dark:text-slate-400">
                            {{ $activity->tanggal_mulai->locale('id')->translatedFormat('d M') }}
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
        @if ($soonActivities->count() > 3)
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                +{{ $soonActivities->count() - 3 }} kegiatan lainnya
            </p>
        @endif
        </aside>
    </template>

    <!-- BANNER HERO -->
    <article class="bg-white rounded-xl border border-slate-200 p-6 sm:p-8 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <section class="max-w-2xl">
            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 mb-2 leading-tight">
                Selamat Datang di <span class="text-blue-700">e-SAKIP</span> BPS Kaltim
            </h2>
            <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                Sistem Akuntabilitas Kinerja Instansi Pemerintah BPS Provinsi Kalimantan Timur.
            </p>
        </section>
    </article>

    <!-- STATISTIK SECTION -->
    <section aria-labelledby="ringkasan-status">
        <h2 id="ringkasan-status" class="text-lg font-semibold text-slate-800 mb-4">Ringkasan Status</h2>
        
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="mb-4 flex items-start justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-500">Belum Mulai</h3>
                    <span class="grid size-10 place-items-center rounded-xl bg-slate-100 text-slate-600 dark:bg-gray-800 dark:text-gray-100">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 7v5l3 2" />
                        </svg>
                    </span>
                </header>
                <p x-data="countUp(@js($statusCounts['notStarted']))" x-text="value" class="mb-1 text-3xl font-bold text-slate-800 dark:text-gray-100">{{ $statusCounts['notStarted'] }}</p>
                <footer class="text-xs text-slate-400">Indikator Kinerja</footer>
            </article>

            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="mb-4 flex items-start justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-500">Sedang Berjalan</h3>
                    <span class="grid size-10 place-items-center rounded-xl bg-blue-50 text-blue-700 dark:bg-gray-800 dark:text-gray-100">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V5m0 14h16M8 15l3-4 3 2 5-6" />
                        </svg>
                    </span>
                </header>
                <p x-data="countUp(@js($statusCounts['inProgress']))" x-text="value" class="mb-1 text-3xl font-bold text-blue-700 dark:text-gray-100">{{ $statusCounts['inProgress'] }}</p>
                <footer class="text-xs text-slate-400">Indikator Kinerja</footer>
            </article>

            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="mb-4 flex items-start justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-500">Selesai</h3>
                    <span class="grid size-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-gray-800 dark:text-gray-100">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m8 12 2.5 2.5L16 9" />
                        </svg>
                    </span>
                </header>
                <p x-data="countUp(@js($statusCounts['completed']))" x-text="value" class="mb-1 text-3xl font-bold text-emerald-600 dark:text-gray-100">{{ $statusCounts['completed'] }}</p>
                <footer class="text-xs text-slate-400">Indikator Kinerja</footer>
            </article>

            <article class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <header class="mb-4 flex items-start justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-500">Deadline 30 Hari</h3>
                    <span class="grid size-10 place-items-center rounded-xl bg-orange-50 text-orange-700 dark:bg-gray-800 dark:text-gray-100">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3 2.8 19a1.2 1.2 0 0 0 1 1.8h16.4a1.2 1.2 0 0 0 1-1.8L12 3Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 3h.01" />
                        </svg>
                    </span>
                </header>
                <p x-data="countUp(@js($statusCounts['deadlines']))" x-text="value" class="mb-1 text-3xl font-bold text-orange-600 dark:text-gray-100">{{ $statusCounts['deadlines'] }}</p>
                <footer class="text-xs text-slate-400">Jadwal Mendatang</footer>
            </article>
        </section>
    </section>

    <!-- KALENDER & KEGIATAN -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <article class="lg:col-span-2 min-w-0 bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-6"
            aria-labelledby="kalender-title">
            <header class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="kalender-title" class="text-lg font-semibold text-slate-800">Kalender Kegiatan</h2>
                    <p class="text-sm text-slate-500">{{ $month->locale('id')->translatedFormat('F Y') }}</p>
                    <p class="text-[11px] text-slate-400 sm:hidden">Ketuk tanggal untuk menambah kegiatan</p>
                </div>
                <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:flex-nowrap">
                    <a href="{{ route('dashboard', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Bulan sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m15 18-6-6 6-6" />
                        </svg>
                    </a>
                    <a href="{{ route('dashboard', ['month' => now()->format('Y-m')]) }}"
                        class="inline-flex h-10 items-center rounded-lg border border-slate-200 px-3 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Hari ini
                    </a>
                    <a href="{{ route('dashboard', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Bulan berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                    <button type="button" @click="openActivityForm()"
                        class="ml-auto inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-blue-700 sm:ml-1 sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 5v14m-7-7h14" />
                        </svg>
                        <span class="hidden sm:inline">Tambah kegiatan</span>
                        <span class="sm:hidden">Tambah</span>
                    </button>
                </div>
            </header>

            <div class="w-full min-w-0">
                <div class="w-full min-w-0">
                    <div class="grid grid-cols-7 border-b border-slate-200 pb-2 text-center text-[10px] font-semibold uppercase tracking-wide text-slate-500 sm:text-[11px] dark:border-slate-700 dark:text-slate-400">
                        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                            <div>{{ $weekday }}</div>
                        @endforeach
                    </div>

                    @foreach ($calendarWeeks as $week)
                        <div class="grid grid-cols-7 auto-rows-[1.5rem] [grid-template-rows:3.5rem] border-b border-slate-100 last:border-b-0 dark:border-slate-800">
                            @foreach ($week['days'] as $day)
                                <section
                                    class="group h-full min-w-0 border-r border-slate-100 px-0 sm:px-1 last:border-r-0 dark:border-slate-800 {{ $day['date']->month === $month->month ? 'text-slate-700' : 'text-slate-400 dark:text-slate-600' }}"
                                    style="grid-column: {{ $day['date']->dayOfWeekIso }}; grid-row: 1;">
                                    <header class="flex h-full items-center justify-between">
                                        @if ($day['hasActivity'])
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full">
                                                <span
                                                    class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold {{ $day['date']->isToday() ? 'bg-blue-600 text-white' : '' }}">
                                                    {{ $day['date']->format('j') }}
                                                </span>
                                            </span>
                                        @else
                                            <button type="button"
                                                data-add-activity-date="{{ $day['date']->toDateString() }}"
                                                @click="openActivityForm('{{ $day['date']->toDateString() }}')"
                                                class="flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-500"
                                                aria-label="Tambah kegiatan tanggal {{ $day['date']->format('d-m-Y') }}"
                                                title="Tambah kegiatan pada tanggal ini">
                                                <span
                                                    class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold transition-colors hover:bg-blue-50 dark:hover:bg-slate-800 {{ $day['date']->isToday() ? 'bg-blue-600 text-white hover:bg-blue-700 dark:hover:bg-blue-700' : '' }}">
                                                    {{ $day['date']->format('j') }}
                                                </span>
                                            </button>
                                            <button type="button"
                                                data-add-activity-date="{{ $day['date']->toDateString() }}"
                                                @click="openActivityForm('{{ $day['date']->toDateString() }}')"
                                                class="hidden h-7 w-7 shrink-0 items-center justify-center rounded text-slate-400 transition-opacity hover:bg-blue-50 hover:text-blue-600 focus-visible:opacity-100 sm:inline-flex sm:opacity-0 sm:group-hover:opacity-100 dark:hover:bg-slate-800"
                                                aria-label="Tambah kegiatan tanggal {{ $day['date']->format('d-m-Y') }}"
                                                title="Tambah kegiatan">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v14m-7-7h14" />
                                                </svg>
                                            </button>
                                        @endif
                                    </header>
                                </section>
                            @endforeach

                            @foreach ($week['bars'] as $bar)
                                <button type="button"
                                    style="grid-column: {{ $bar['startColumn'] }} / span {{ $bar['columnSpan'] }}; grid-row: {{ $bar['lane'] + 2 }};"
                                    data-activity="{{ json_encode([
                                        'id' => $bar['activity']->id,
                                        'judul' => $bar['activity']->judul,
                                        'kategori' => $bar['activity']->kategori,
                                        'tanggalMulai' => $bar['activity']->tanggal_mulai->locale('id')->translatedFormat('d M Y'),
                                        'tanggalSelesai' => $bar['activity']->tanggal_selesai->locale('id')->translatedFormat('d M Y'),
                                        'deskripsi' => $bar['activity']->deskripsi,
                                        'selesai' => $bar['activity']->selesai,
                                    ]) }}"
                                    @click="openActivityDetails(JSON.parse($event.currentTarget.dataset.activity))"
                                    class="z-10 flex h-full min-w-0 items-center gap-0.5 rounded-full border-l-2 px-1 text-left text-[9px] transition hover:brightness-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-500 sm:gap-2 sm:border-l-4 sm:px-2 sm:text-[10px] {{ $bar['activity']->selesai ? 'border-emerald-500 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200' : 'border-blue-500 bg-blue-50 text-blue-800 dark:bg-blue-950/60 dark:text-blue-200' }}"
                                    aria-label="Lihat kegiatan: {{ $bar['activity']->judul }}"
                                    title="{{ $bar['activity']->judul }} ({{ $bar['activity']->kategori }})">
                                    @if ($bar['activity']->selesai)
                                        <svg class="h-3 w-3 shrink-0 text-emerald-600 dark:text-emerald-300 sm:h-4 sm:w-4"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <circle cx="12" cy="12" r="9" stroke-width="2" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="m8 12 2.5 2.5L16 9" />
                                        </svg>
                                    @endif
                                    <span
                                        class="min-w-0 truncate {{ $bar['activity']->selesai ? 'line-through opacity-70' : 'font-semibold' }}">
                                        {{ $bar['activity']->judul }}
                                    </span>
                                    <span class="hidden shrink-0 text-[9px] md:inline">
                                        {{ $bar['activity']->selesai ? 'Selesai' : $bar['activity']->kategori }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6"
            aria-labelledby="kegiatan-title">
            <header class="mb-4">
                <h2 id="kegiatan-title" class="text-lg font-semibold text-slate-800">Kegiatan Mendatang</h2>
                <p class="text-xs text-slate-400">30 hari ke depan</p>
            </header>
            <ul class="space-y-3">
                @forelse ($upcomingActivities as $activity)
                    <li class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3 last:border-0">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-700">{{ $activity->judul }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $activity->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}
                                @if (!$activity->tanggal_mulai->isSameDay($activity->tanggal_selesai))
                                    – {{ $activity->tanggal_selesai->locale('id')->translatedFormat('d M Y') }}
                                @endif
                            </p>
                            <p class="text-[10px] text-slate-400">{{ $activity->kategori }}</p>
                        </div>
                        <button type="button"
                            data-activity="{{ json_encode([
                                'id' => $activity->id,
                                'judul' => $activity->judul,
                                'kategori' => $activity->kategori,
                                'tanggalMulai' => $activity->tanggal_mulai->locale('id')->translatedFormat('d M Y'),
                                'tanggalSelesai' => $activity->tanggal_selesai->locale('id')->translatedFormat('d M Y'),
                                'deskripsi' => $activity->deskripsi,
                                'selesai' => $activity->selesai,
                            ]) }}"
                            @click="openDeleteConfirmation(JSON.parse($event.currentTarget.dataset.activity))"
                            class="rounded p-1 text-slate-400 hover:bg-red-50 hover:text-red-600"
                            aria-label="Hapus {{ $activity->judul }}" title="Hapus kegiatan">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 7h12m-10 0V5h8v2m-9 0 1 13h8l1-13M10 11v5m4-5v5" />
                            </svg>
                        </button>
                    </li>
                @empty
                    <li class="flex min-h-40 flex-col items-center justify-center text-center text-slate-500">
                        <svg class="mb-2 h-7 w-7 text-slate-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                        </svg>
                        <p class="text-sm">Belum ada kegiatan terdaftar.</p>
                        <button type="button" @click="openActivityForm()"
                            class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                            Tambah kegiatan
                        </button>
                    </li>
                @endforelse
            </ul>
        </article>
    </section>

    <template x-teleport="body">
    <div x-cloak x-show="activityDetailsOpen" x-transition.opacity
        @keydown.escape.window="activityDetailsOpen = false"
        @click.self="activityDetailsOpen = false"
        class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-950/60 p-4"
        role="presentation">
        <section x-show="activityDetailsOpen" x-transition
            class="my-auto w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900"
            role="dialog" aria-modal="true" aria-labelledby="activity-details-title">
            <header class="mb-5 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-blue-600"
                        x-text="selectedActivity.kategori"></p>
                    <h2 id="activity-details-title" class="break-words text-xl font-bold text-slate-900 dark:text-slate-100"
                        x-text="selectedActivity.judul"></h2>
                </div>
                <button type="button" @click="activityDetailsOpen = false"
                    class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                    aria-label="Tutup detail kegiatan">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </header>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="font-medium text-slate-500 dark:text-slate-400">Tanggal</dt>
                    <dd class="text-slate-800 dark:text-slate-200">
                        <span x-text="selectedActivity.tanggalMulai"></span>
                        <template x-if="selectedActivity.tanggalMulai !== selectedActivity.tanggalSelesai">
                            <span> – <span x-text="selectedActivity.tanggalSelesai"></span></span>
                        </template>
                    </dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-500 dark:text-slate-400">Status</dt>
                    <dd class="inline-flex items-center gap-1.5 font-semibold text-slate-800 dark:text-slate-200">
                        <svg x-show="selectedActivity.selesai" class="h-4 w-4 text-emerald-600 dark:text-emerald-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke-width="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m8 12 2.5 2.5L16 9" />
                        </svg>
                        <span x-text="selectedActivity.selesai ? 'Selesai' : 'Belum selesai'"></span>
                    </dd>
                </div>
                <div x-show="selectedActivity.deskripsi">
                    <dt class="font-medium text-slate-500 dark:text-slate-400">Keterangan</dt>
                    <dd class="whitespace-pre-line text-slate-800 dark:text-slate-200"
                        x-text="selectedActivity.deskripsi"></dd>
                </div>
            </dl>

            <footer class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <button type="button" @click="openDeleteConfirmation()"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">
                    Hapus
                </button>
                <div class="flex gap-2">
                    <button type="button" @click="activityDetailsOpen = false"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Tutup
                    </button>
                    <form :action="'{{ url('/jadwals') }}/' + selectedActivity.id + '/completion'" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="selesai" :value="selectedActivity.selesai ? 0 : 1">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                            x-text="selectedActivity.selesai ? 'Tandai belum selesai' : 'Tandai selesai'">
                        </button>
                    </form>
                </div>
            </footer>
        </section>
    </div>
    </template>

    <template x-teleport="body">
    <div x-cloak x-show="deleteConfirmationOpen" x-transition.opacity
        @keydown.escape.window="deleteConfirmationOpen = false"
        @click.self="deleteConfirmationOpen = false"
        class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4"
        role="presentation">
        <section x-show="deleteConfirmationOpen" x-transition
            class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900"
            role="alertdialog" aria-modal="true" aria-labelledby="delete-activity-title"
            aria-describedby="delete-activity-description">
            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 7h12m-10 0V5h8v2m-9 0 1 13h8l1-13M10 11v5m4-5v5" />
                </svg>
            </div>
            <h2 id="delete-activity-title" class="text-lg font-bold text-slate-900 dark:text-slate-100">
                Hapus kegiatan?
            </h2>
            <p id="delete-activity-description" class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                <span x-text="selectedActivity.judul"></span> akan dihapus permanen.
            </p>
            <footer class="mt-6 flex justify-end gap-3">
                <button type="button" @click="deleteConfirmationOpen = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Batal
                </button>
                <form :action="'{{ url('/jadwals') }}/' + selectedActivity.id" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        Ya, hapus
                    </button>
                </form>
            </footer>
        </section>
    </div>
    </template>

    <template x-teleport="body">
    <div x-cloak x-show="activityFormOpen" x-transition.opacity
        @keydown.escape.window="activityFormOpen = false"
        @click.self="activityFormOpen = false"
        class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-950/60 p-4"
        role="presentation">
        <section x-show="activityFormOpen" x-transition
            class="my-auto w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900"
            role="dialog" aria-modal="true" aria-labelledby="activity-form-title">
            <header class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 id="activity-form-title" class="text-xl font-bold text-slate-900 dark:text-slate-100">
                        Tambah kegiatan
                    </h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Catat kegiatan agar tampil di kalender.
                    </p>
                </div>
                <button type="button" @click="activityFormOpen = false"
                    class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                    aria-label="Tutup formulir">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>
            </header>

            <form action="{{ route('jadwals.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="activity-title" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Nama kegiatan
                    </label>
                    <input id="activity-title" name="judul" type="text" value="{{ old('judul') }}" required
                        maxlength="255"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    @error('judul')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="activity-category" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Kategori
                    </label>
                    <select id="activity-category" name="kategori" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                        <option value="">Pilih kategori</option>
                        @foreach (['FRA', 'KKPK', 'Renstra', 'LAKIN'] as $category)
                            <option value="{{ $category }}" @selected(old('kategori') === $category)>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="activity-start" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Tanggal mulai
                        </label>
                        <input id="activity-start" name="tanggal_mulai" type="date" x-model="activityStartDate"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                        @error('tanggal_mulai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="activity-end" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            Tanggal selesai
                        </label>
                        <input id="activity-end" name="tanggal_selesai" type="date" x-model="activityEndDate"
                            required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                        @error('tanggal_selesai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="activity-description" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        Keterangan <span class="font-normal text-slate-400">(opsional)</span>
                    </label>
                    <textarea id="activity-description" name="deskripsi" rows="3" maxlength="2000"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <footer class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="activityFormOpen = false"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan kegiatan
                    </button>
                </footer>
            </form>
        </section>
    </div>
    </template>
</section>
@endsection