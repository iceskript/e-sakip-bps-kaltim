<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'username', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (! filled($user->username)) {
                $user->username = self::usernameForEmail($user->email);
            }
        });
    }

    public static function usernameForEmail(string $email): string
    {
        $base = preg_replace('/[^a-z0-9._-]/', '', Str::lower(Str::before($email, '@'))) ?: 'user';
        $base = substr($base, 0, 50);
        $username = $base;
        $suffix = 2;

        while (static::query()->where('username', $username)->exists()) {
            $suffixText = '-'.$suffix;
            $username = substr($base, 0, 50 - strlen($suffixText)).$suffixText;
            $suffix++;
        }

        return $username;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array(Str::lower($this->email), config('auth.admin_emails', []), true);
    }
}
