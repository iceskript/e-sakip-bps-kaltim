<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    public function show(): View
    {
        return view('auth.login', [
            'googleConfigured' => $this->googleIsConfigured(),
        ]);
    }

    public function redirectToGoogle(): RedirectResponse
    {
        abort_unless($this->googleIsConfigured(), 503, 'Google sign-in is not configured.');

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        abort_unless($this->googleIsConfigured(), 503, 'Google sign-in is not configured.');

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $exception) {
            report($exception);

            return to_route('login')->withErrors([
                'google' => 'Sesi masuk Google berakhir. Silakan coba lagi.',
            ]);
        }

        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $googleAttributes = $googleUser->getRaw();
        $emailIsVerified = filter_var(
            $googleAttributes['email_verified'] ?? $googleAttributes['verified_email'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false || ! $emailIsVerified) {
            return to_route('login')->withErrors([
                'google' => 'Akun Google harus memiliki alamat email terverifikasi.',
            ]);
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if (! $user && ! in_array($email, config('services.google.allowed_emails', []), true)) {
            return to_route('login')->withErrors([
                'google' => 'Alamat Google ini belum terdaftar. Hubungi administrator.',
            ]);
        }

        if (! $user) {
            $name = trim((string) $googleUser->getName());
            $user = User::create([
                'email' => $email,
                'username' => User::usernameForEmail($email),
                'name' => $name !== '' ? $name : Str::before($email, '@'),
                'password' => Str::random(64),
                'is_active' => true,
            ]);
        }

        if (! $user->is_active) {
            return to_route('login')->withErrors([
                'google' => 'Akun ini tidak aktif. Hubungi administrator.',
            ]);
        }

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }

    private function googleIsConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'));
    }
}
