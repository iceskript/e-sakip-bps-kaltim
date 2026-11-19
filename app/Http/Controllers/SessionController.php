<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'username' => Str::lower(trim((string) $request->input('username'))),
        ]);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9._-]+$/'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Username atau kata sandi tidak valid.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
