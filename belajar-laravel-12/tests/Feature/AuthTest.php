<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase; // Untuk mereset database setelah setiap test

    public function test_user_can_view_register_page()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/login'); // Setelah register, redirect ke login
        $response->assertSessionHas('success', 'Registrasi berhasil, silahkan login.');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_validation_errors()
    {
        $userData = [
            'name' => '', // Nama kosong
            'email' => 'invalid-email', // Email tidak valid
            'password' => 'short', // Password terlalu pendek
            'password_confirmation' => 'mismatch', // Password tidak cocok
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_user_can_view_login_page()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'), // Hash password untuk login
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertRedirect('/users'); // Redirect setelah login sukses
        $this->assertAuthenticatedAs($user); // Memastikan user terautentikasi
    }

    public function test_login_with_invalid_credentials()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrong-password',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertSessionHasErrors(['email']); // Error email/password salah
        $this->assertGuest(); // Memastikan tidak ada user yang terautentikasi
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout'); // Login user dulu, lalu logout

        $response->assertRedirect('/login'); // Redirect ke login setelah logout
        $this->assertGuest(); // Memastikan user tidak lagi terautentikasi
    }
}