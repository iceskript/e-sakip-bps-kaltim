@extends('layouts.app')

@section('title', 'Kelola akun')

@section('content')
    <section x-data="{ deleteOpen: false, deleteAction: '', deleteName: '' }"
        @keydown.escape.window="deleteOpen = false" class="mx-auto max-w-6xl space-y-6">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700 dark:text-sky-300">
                    Administrasi
                </p>
                <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Kelola akun</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Buat akun internal dan kelola akses pengguna.
                </p>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $users->count() }} akun terdaftar</p>
        </header>

        @if (session('status'))
            <div role="status"
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert"
                class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)]">
            <article class="h-fit rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Buat akun</h3>
                <p class="mt-1 text-sm leading-5 text-slate-500 dark:text-slate-400">
                    Email dan kata sandi dapat digunakan langsung. Akun dengan email Google juga dapat memakai Google SSO.
                </p>

                <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Nama
                        </label>
                        <input id="name" name="name" value="{{ old('name') }}" required maxlength="255"
                            autocomplete="name"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Username
                        </label>
                        <input id="username" name="username" value="{{ old('username') }}" required maxlength="50"
                            autocomplete="username" pattern="[A-Za-z0-9._-]+"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('username')
                            <p class="mt-1.5 text-xs text-rose-700 dark:text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Email
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                            autocomplete="email"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Kata sandi awal
                        </label>
                        <input id="password" name="password" type="password" required minlength="12"
                            autocomplete="new-password"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Ulangi kata sandi
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="12"
                            autocomplete="new-password"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <button type="submit"
                        class="flex min-h-11 w-full items-center justify-center rounded-lg bg-[#123b50] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0e3041] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900">
                        Tambahkan akun
                    </button>
                </form>
            </article>

            <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Akun terdaftar</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Nonaktifkan akun untuk mencabut akses tanpa menghapus datanya.
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950 dark:text-slate-400">
                            <tr>
                                <th scope="col" class="px-5 py-3 font-semibold sm:px-6">Pengguna</th>
                                <th scope="col" class="px-5 py-3 font-semibold">Status</th>
                                <th scope="col" class="px-5 py-3 text-right font-semibold sm:px-6">Akses</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-5 py-4 sm:px-6">
                                        <p class="font-medium text-slate-900 dark:text-slate-100">{{ $user->name }}</p>
                                        <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300">{{ $user->username }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                            {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        @if ($user->isAdmin())
                                            <span class="ml-1 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                                                Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right sm:px-6">
                                        <div class="flex flex-col items-end gap-2">
                                            @if (! $user->isAdmin() && ! $user->is(auth()->user()))
                                                <form method="POST" action="{{ route('admin.users.status.update', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                                    <button type="submit"
                                                        class="rounded-md px-2.5 py-1.5 text-xs font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600
                                                            {{ $user->is_active ? 'text-rose-700 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/30' : 'text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-950/30' }}">
                                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                </form>
                                                <button type="button"
                                                    @click="deleteAction = @js(route('admin.users.destroy', $user)); deleteName = @js($user->name); deleteOpen = true"
                                                    class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600 dark:text-rose-300 dark:hover:bg-rose-950/30">
                                                    Hapus
                                                </button>
                                            @else
                                                <span class="text-xs text-slate-400">Dilindungi</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                        Belum ada akun.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>

        <template x-teleport="body">
            <div x-cloak x-show="deleteOpen" x-transition.opacity
                @click.self="deleteOpen = false"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-950/50 p-4"
                role="presentation">
                <section x-show="deleteOpen" x-transition
                    class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-5 shadow-2xl dark:border-slate-700 dark:bg-slate-900 sm:p-6"
                    role="dialog" aria-modal="true" aria-labelledby="delete-account-title">
                    <h2 id="delete-account-title" class="text-lg font-semibold text-slate-900 dark:text-white">
                        Hapus akun?
                    </h2>
                    <p class="mt-2 text-sm leading-5 text-slate-600 dark:text-slate-400">
                        Akun <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="deleteName"></span>
                        akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <form method="POST" x-bind:action="deleteAction" class="mt-6 flex justify-end gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteOpen = false"
                            class="min-h-10 rounded-lg border border-slate-300 px-3.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                            Batal
                        </button>
                        <button type="submit"
                            class="min-h-10 rounded-lg bg-rose-700 px-4 text-sm font-semibold text-white hover:bg-rose-800">
                            Hapus akun
                        </button>
                    </form>
                </section>
            </div>
        </template>
    </section>
@endsection
