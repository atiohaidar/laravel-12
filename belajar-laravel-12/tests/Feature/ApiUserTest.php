<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;
    private $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('api-token')->plainTextToken;
        Sanctum::actingAs($this->user); // Login user untuk test CRUD user API
    }

    public function test_authenticated_user_can_view_users_index_api()
    {
        $response = $this->getJson('/api/users', ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJsonStructure(['users' => []])
            ->assertJsonFragment(['id' => $this->user->id, 'name' => $this->user->name, 'email' => $this->user->email]); // Memastikan data user ada di response
    }

    public function test_unauthenticated_user_cannot_view_users_index_api()
    {
        $this->app->make('auth')->forgetGuards();

        $response = $this->getJson('/api/users'); // Tanpa token

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_authenticated_user_can_create_user_api()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/users', $userData, ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email']])
            ->assertJson(['message' => 'User berhasil ditambahkan']);
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    public function test_create_user_api_validation_errors()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ];

        $response = $this->postJson('/api/users', $userData, ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_authenticated_user_can_view_user_show_api()
    {
        $response = $this->getJson('/api/users/' . $this->user->id, ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user' => ['id', 'name', 'email']])
            ->assertJson(['user' => ['id' => $this->user->id, 'name' => $this->user->name, 'email' => $this->user->email]]);
    }

    public function test_unauthenticated_user_cannot_view_user_show_api()
    {
        $this->app->make('auth')->forgetGuards();

        $response = $this->getJson('/api/users/' . $this->user->id);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_authenticated_user_can_update_user_api()
    {
        $updatedData = [
            'name' => 'Updated Name API',
            'email' => 'updated.api@example.com',
        ];

        $response = $this->putJson('/api/users/' . $this->user->id, $updatedData, ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email']])
            ->assertJson(['message' => 'User berhasil diupdate', 'user' => ['id' => $this->user->id, 'name' => 'Updated Name API', 'email' => 'updated.api@example.com']]);
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'name' => 'Updated Name API', 'email' => 'updated.api@example.com']);
    }

    public function test_update_user_api_validation_errors()
    {
        $updatedData = [
            'name' => '',
            'email' => 'invalid-email',
        ];

        $response = $this->putJson('/api/users/' . $this->user->id, $updatedData, ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_authenticated_user_can_delete_user_api()
    {
        $response = $this->deleteJson('/api/users/' . $this->user->id, [], ['Authorization' => 'Bearer ' . $this->token]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'User berhasil dihapus']);
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    public function test_unauthenticated_user_cannot_delete_user_api()
    {
        // Clear the authenticated user for this test
        $this->app->make('auth')->forgetGuards();
        
        $response = $this->deleteJson('/api/users/' . $this->user->id);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}