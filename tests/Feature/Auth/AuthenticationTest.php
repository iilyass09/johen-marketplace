<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create(['username' => 'tester']);

        $response = $this->post('/login', [
            'username' => 'tester',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_users_can_authenticate_using_their_email(): void
    {
        $user = User::factory()->create(['username' => 'tester']);

        $response = $this->post('/login', [
            'username' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create(['username' => 'tester']);

        $response = $this->post('/login', [
            'username' => 'tester',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_login_requires_username_field(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('username');
    }

    public function test_admin_is_redirected_to_admin_login(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'is_admin' => true,
        ]);

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
