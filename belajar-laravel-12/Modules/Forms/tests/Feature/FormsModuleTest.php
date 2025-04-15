<?php

namespace Modules\Forms\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Modules\Forms\app\Models\Form;

class FormsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('forms.store'), [
            'title' => 'Test Form',
            'description' => 'This is a test form',
            'is_public' => true,
            'collect_email' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('forms', [
            'title' => 'Test Form',
            'user_id' => $user->id,
        ]);
    }
}