<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialUser;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    private function googleUser(array $attributes = []): SocialUser
    {
        $googleUser = new SocialUser;
        $googleUser->id = 'google-id-123456';
        $googleUser->name = 'Budi Google';
        $googleUser->email = 'budi@gmail.com';
        $googleUser->avatar = 'https://example.com/avatar-budi.png';

        foreach ($attributes as $key => $value) {
            $googleUser->{$key} = $value;
        }

        return $googleUser;
    }

    private function mockGoogleProvider(SocialUser $googleUser): void
    {
        $driver = \Mockery::mock();
        $driver->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->andReturn($driver);
    }

    public function test_google_login_creates_new_account(): void
    {
        $this->mockGoogleProvider($this->googleUser());

        $this->get('/auth/google/callback')->assertRedirect(route('home'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'budi@gmail.com',
            'google_id' => 'google-id-123456',
            'avatar' => 'https://example.com/avatar-budi.png',
        ]);

        $user = User::where('email', 'budi@gmail.com')->firstOrFail();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotSame('budi@gmail.com', $user->username);
    }

    public function test_google_login_links_existing_email_account(): void
    {
        $user = User::factory()->create([
            'email' => 'budi@gmail.com',
            'google_id' => null,
            'email_verified_at' => null,
        ]);

        $this->mockGoogleProvider($this->googleUser());

        $this->get('/auth/google/callback')->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::count());

        $user->refresh();
        $this->assertSame('google-id-123456', $user->google_id);
        $this->assertSame('https://example.com/avatar-budi.png', $user->avatar);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_google_login_uses_existing_google_account(): void
    {
        $user = User::factory()->create([
            'email' => 'budi@gmail.com',
            'google_id' => 'google-id-123456',
        ]);

        $this->mockGoogleProvider($this->googleUser());

        $this->get('/auth/google/callback')->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::count());
    }

    public function test_google_login_failure_redirects_back_with_error(): void
    {
        $driver = \Mockery::mock();
        $driver->shouldReceive('user')->andThrow(new \Exception('User denied access'));

        Socialite::shouldReceive('driver')->andReturn($driver);

        $this->get('/auth/google/callback')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertGuest();
        $this->assertSame(0, User::count());
    }

    public function test_google_redirect_blocked_when_not_configured(): void
    {
        config(['services.google.client_id' => null, 'services.google.client_secret' => null]);

        $this->get('/auth/google')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }
}