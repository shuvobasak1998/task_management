<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_user_can_register_and_access_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Shuvo Dev',
            'email' => 'shuvo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post('/logout');

        $logoutResponse->assertRedirect('/login');
        $this->assertGuest();
    }
}
