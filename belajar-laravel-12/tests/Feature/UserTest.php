<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker; // Untuk data palsu (nama, email dll)

    public function test_user_can_view_users_index_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/users');

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertSee('Daftar User'); // Memastikan judul halaman ada
    }

    public function test_guest_cannot_view_users_index_page()
    {
        $response = $this->get('/users');

        $response->assertRedirect('/login'); // Guest harus redirect ke login
    }

    public function test_user_can_view_create_user_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/users/create');

        $response->assertStatus(200);
        $response->assertViewIs('users.create');
    }

    public function test_guest_cannot_view_create_user_page()
    {
        $response = $this->get('/users/create');

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_user()
    {
        $user = User::factory()->create();
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->actingAs($user)->post('/users', $userData);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User berhasil ditambahkan.');
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }

    public function test_create_user_validation_errors()
    {
        $user = User::factory()->create();
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ];

        $response = $this->actingAs($user)->post('/users', $userData);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_user_can_view_user_show_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/users/' . $user->id);

        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    public function test_guest_cannot_view_user_show_page()
    {
        $user = User::factory()->create();
        $response = $this->get('/users/' . $user->id);

        $response->assertRedirect('/login');
    }

    public function test_user_can_view_user_edit_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/users/' . $user->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertSee('Edit User');
        $response->assertSee($user->name);
    }

    public function test_guest_cannot_view_user_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->get('/users/' . $user->id . '/edit');

        $response->assertRedirect('/login');
    }

    public function test_user_can_update_user()
    {
        $user = User::factory()->create();
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($user)->put('/users/' . $user->id, $updatedData);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User berhasil diupdate.');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name', 'email' => 'updated@example.com']);
    }

    public function test_update_user_validation_errors()
    {
        $user = User::factory()->create();
        $updatedData = [
            'name' => '',
            'email' => 'invalid-email',
        ];

        $response = $this->actingAs($user)->put('/users/' . $user->id, $updatedData);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_user_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/users/' . $user->id);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User berhasil dihapus.');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_guest_cannot_delete_user()
    {
        $user = User::factory()->create();
        $response = $this->delete('/users/' . $user->id);

        $response->assertRedirect('/login');
    }
}