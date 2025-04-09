<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_api()
    {
        $userData = [
            'name' => 'Test User API',
            'email' => 'api.test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'token'])
            ->assertJson(['message' => 'Registrasi berhasil']);
        $this->assertDatabaseHas('users', ['email' => 'api.test@example.com']);
    }

    public function test_register_api_validation_errors()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_api()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'token'])
            ->assertJson(['message' => 'Login berhasil']);
        $this->assertNotEmpty($response->json('token')); // Memastikan token ada di response
    }

    public function test_login_api_invalid_credentials()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrong-password',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    public function test_user_can_logout_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->postJson('/api/logout', [], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil']);
        $this->assertEmpty($user->tokens()->get()); // Memastikan token user dihapus
    }

    public function test_unauthenticated_user_cannot_access_protected_api_route()
    {
        $response = $this->getJson('/api/user'); // Route /api/user dilindungi auth:sanctum
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']); // Default error message dari Sanctum
    }

    public function test_authenticated_user_can_access_protected_api_route()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user); // Mensimulasikan user login menggunakan Sanctum untuk API test

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
    }
}