<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'username' => Str::lower(trim((string) $request->input('username'))),
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ]);

        return to_route('admin.users.index')->with('status', 'Akun berhasil dibuat.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);
        $isActive = (bool) $validated['is_active'];

        if (! $isActive && ($user->is(auth()->user()) || $user->isAdmin())) {
            return back()->withErrors([
                'is_active' => 'Akun admin atau akun yang sedang digunakan tidak dapat dinonaktifkan.',
            ]);
        }

        $user->update(['is_active' => $isActive]);

        return to_route('admin.users.index')->with(
            'status',
            $isActive ? 'Akun berhasil diaktifkan.' : 'Akun berhasil dinonaktifkan.'
        );
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user()) || $user->isAdmin()) {
            return back()->withErrors([
                'user' => 'Akun admin atau akun yang sedang digunakan tidak dapat dihapus.',
            ]);
        }

        $user->delete();

        return to_route('admin.users.index')->with('status', 'Akun berhasil dihapus.');
    }
}
