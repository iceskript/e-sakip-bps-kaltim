@extends('layouts.guest')

@section('title', 'Atur ulang password')

@section('content')
    <main class="flex min-h-screen items-center justify-center px-4 py-8">
        <section class="w-full max-w-sm">
            <a href="{{ route('login') }}" class="mb-6 flex items-center gap-3">
                <img src="{{ asset('images/logo.webp') }}" alt="Logo BPS" class="h-11 w-11 bg-transparent object-contain">
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-100">e-SAKIP BPS Kaltim</span>
            </a>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
                <h1 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white">Atur ulang password</h1>
                <p class="mt-2 text-sm leading-5 text-slate-600 dark:text-slate-400">
                    Buat password baru untuk akun Anda.
                </p>

                @if ($errors->any())
                    <div class="mt-5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-3 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-200"
                        role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Email
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required
                            autocomplete="email"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Password baru
                        </label>
                        <input id="password" name="password" type="password" required minlength="12"
                            autocomplete="new-password"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Ulangi password baru
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="12"
                            autocomplete="new-password"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    </div>
                    <button type="submit"
                        class="flex min-h-11 w-full items-center justify-center rounded-lg bg-[#123b50] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0e3041] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900">
                        Simpan password baru
                    </button>
                </form>
            </div>
        </section>
    </main>
@endsection
