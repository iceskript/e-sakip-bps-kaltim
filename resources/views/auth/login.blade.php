@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <main class="flex min-h-screen items-center justify-center bg-[#f5f7f6] px-4 py-8">
        <section class="w-full max-w-sm">
            <header class="login-brand mb-6 flex items-center justify-center gap-3">
                <img src="{{ asset('images/logo.webp') }}" alt="Logo BPS"
                    class="h-12 w-12 shrink-0 bg-transparent object-contain">
                <span class="text-sm font-semibold text-slate-800">e-SAKIP BPS Kaltim</span>
            </header>

            <div class="login-card rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Login</h1>

                @if (session('status'))
                    <div class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 px-3.5 py-3 text-sm text-emerald-800"
                        role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->has('google') || $errors->has('username'))
                    <div class="mt-5 rounded-lg border border-rose-200 bg-rose-50 px-3.5 py-3 text-sm text-rose-800"
                        role="alert">
                        {{ $errors->first('google') ?: $errors->first('username') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Username
                        </label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required
                            maxlength="50" autocomplete="username" autofocus placeholder="Masukkan username"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15">
                        @error('username')
                            <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-medium text-blue-700 hover:underline">
                                Lupa password?
                            </a>
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            placeholder="Masukkan password"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/15">
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <label for="remember" class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                            <input id="remember" name="remember" type="checkbox" value="1"
                                class="h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                            Ingat saya
                        </label>
                    </div>
                    <button type="submit"
                        class="flex min-h-11 w-full items-center justify-center rounded-lg bg-[#123b50] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0e3041] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        Login
                    </button>
                </form>

                @if ($googleConfigured)
                    <div class="my-5 flex items-center gap-3 text-xs text-slate-400" aria-hidden="true">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        <span>atau</span>
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>
                    <a href="{{ route('auth.google.redirect') }}"
                        class="flex min-h-11 w-full items-center justify-center gap-2.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09Z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23Z" />
                            <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.62Z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52Z" />
                        </svg>
                        Masuk dengan Google
                    </a>
                @else
                    <p class="mt-5 text-center text-xs text-slate-500">Login Google belum tersedia.</p>
                @endif
            </div>
        </section>
    </main>
@endsection
