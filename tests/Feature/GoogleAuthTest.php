<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_protected_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('ikss.index'))->assertRedirect(route('login'));
    }

    public function test_login_page_is_light_only_and_uses_a_username_field(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('Username')
            ->assertSee('name="username"', false)
            ->assertSee('Lupa password?')
            ->assertDontSee('name="email"', false)
            ->assertDontSee('toggleTheme')
            ->assertDontSee('dark:')
            ->assertSee('login-card')
            ->assertDontSee('Akun dibuat oleh admin');
    }

    public function test_login_page_shows_internal_login_when_google_is_not_configured(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Login')
            ->assertSee('name="username"', false)
            ->assertSee('name="password"', false);
    }

    public function test_verified_google_user_is_created_and_authenticated(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'client-secret',
            'services.google.allowed_emails' => ['staff@example.com'],
        ]);
        Socialite::fake('google', $this->googleUser(true));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('dashboard'));

        $user = User::where('email', 'staff@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.com',
            'name' => 'Staff Example',
            'username' => 'staff',
        ]);
    }

    public function test_google_user_with_unverified_email_is_rejected(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'client-secret',
            'services.google.allowed_emails' => ['staff@example.com'],
        ]);
        Socialite::fake('google', $this->googleUser(false));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_unregistered_verified_google_user_is_rejected(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'client-secret',
            'services.google.allowed_emails' => ['approved@example.com'],
        ]);
        Socialite::fake('google', $this->googleUser(true));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registered_internal_user_can_sign_in_with_google_without_bootstrap_allowlist(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'client-secret',
            'services.google.allowed_emails' => [],
        ]);
        User::factory()->create(['email' => 'staff@example.com']);
        Socialite::fake('google', $this->googleUser(true));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs(User::where('email', 'staff@example.com')->firstOrFail());
    }

    public function test_internal_user_can_sign_in_with_password(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@example.com',
            'password' => 'strong-password-123',
        ]);

        $this->post(route('login.store'), [
            'username' => 'STAFF',
            'password' => 'strong-password-123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_internal_user_cannot_sign_in(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'strong-password-123',
            'is_active' => false,
        ]);

        $this->post(route('login.store'), [
            'username' => 'inactive',
            'password' => 'strong-password-123',
        ])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_sign_out(): void
    {
        $user = User::factory()->create();
        config(['services.google.allowed_emails' => [strtolower($user->email)]]);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_session_for_deactivated_user_is_revoked(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_create_internal_accounts(): void
    {
        config(['auth.admin_emails' => ['admin@example.com']]);
        $admin = User::factory()->create(['email' => 'admin@example.com']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Internal Staff',
                'username' => 'staff-account',
                'email' => 'STAFF@example.com',
                'password' => 'strong-password-123',
                'password_confirmation' => 'strong-password-123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.com',
            'username' => 'staff-account',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_and_deactivate_an_account(): void
    {
        config(['auth.admin_emails' => ['admin@example.com']]);
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $user = User::factory()->create(['email' => 'staff@example.com']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Kelola akun')
            ->assertSee('staff@example.com')
            ->assertSee('Pengaturan akun')
            ->assertSee('aria-label="Tema terang"', false)
            ->assertSee('aria-label="Tema gelap"', false)
            ->assertSee('aria-label="Profil pengguna"', false)
            ->assertSee('aria-label="Menu akun"', false)
            ->assertSee('theme-slider-indicator');

        $this->patch(route('admin.users.status.update', $user), ['is_active' => false])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_delete_a_regular_account_but_not_an_admin_or_their_own_account(): void
    {
        config(['auth.admin_emails' => ['admin@example.com']]);
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $staff = User::factory()->create(['email' => 'staff@example.com']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $staff))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $staff->id]);

        $this->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_user_can_request_a_password_reset_without_disclosing_account_existence(): void
    {
        $user = User::factory()->create(['email' => 'staff@example.com']);
        Notification::fake();

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Lupa password');

        $this->post(route('password.email'), ['email' => 'STAFF@example.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);

        $this->post(route('password.email'), ['email' => 'missing@example.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertCount(1);
    }

    public function test_user_can_reset_password_with_a_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'staff@example.com']);
        $token = Password::broker()->createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('Atur ulang password');

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-strong-password', $user->fresh()->password));
    }

    public function test_authenticated_user_can_change_their_password(): void
    {
        $user = User::factory()->create(['password' => 'old-strong-password']);

        $this->actingAs($user)
            ->patch(route('account.password.update'), [
                'current_password' => 'old-strong-password',
                'new_password' => 'new-strong-password',
                'new_password_confirmation' => 'new-strong-password',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-strong-password', $user->fresh()->password));
    }

    public function test_non_admin_cannot_access_account_management(): void
    {
        config(['auth.admin_emails' => ['admin@example.com']]);
        $user = User::factory()->create(['email' => 'staff@example.com']);

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    private function googleUser(bool $emailIsVerified): GoogleUser
    {
        $googleUser = GoogleUser::fake([
            'id' => 'google-user-1',
            'name' => 'Staff Example',
            'email' => 'staff@example.com',
        ]);
        $googleUser->setRaw(['email_verified' => $emailIsVerified]);

        return $googleUser;
    }
}
