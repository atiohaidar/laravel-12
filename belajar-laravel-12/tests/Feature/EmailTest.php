<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\KirimEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_send_email()
    {
        Mail::fake();

        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post("/users/{$recipient->id}/send-email", [
                'message' => 'This is a test email message content.'
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        Mail::assertQueued(KirimEmail::class, function ($mail) use ($recipient) {
            return $mail->hasTo($recipient->email) &&
                   $mail->messageContent === 'This is a test email message content.' &&
                   $mail->user->id === $recipient->id;
        });
    }

    public function test_unauthenticated_user_cannot_send_email()
    {
        $recipient = User::factory()->create();

        $response = $this->post("/users/{$recipient->id}/send-email", [
            'message' => 'This is a test email message.'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_cannot_send_email_with_empty_message()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post("/users/{$recipient->id}/send-email", [
                'message' => ''
            ]);

        $response->assertSessionHasErrors('message');
    }

    public function test_cannot_send_email_with_short_message()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post("/users/{$recipient->id}/send-email", [
                'message' => 'Short'
            ]);

        $response->assertSessionHasErrors('message');
    }

    public function test_cannot_send_email_to_nonexistent_user()
    {
        $sender = User::factory()->create();

        $response = $this->actingAs($sender)
            ->post("/users/999999/send-email", [
                'message' => 'This is a test email message.'
            ]);

        $response->assertStatus(404);
    }

    public function test_email_content_is_correct()
    {
        Mail::fake();

        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $message = 'This is a test email message content.';

        $this->actingAs($sender)
            ->post("/users/{$recipient->id}/send-email", [
                'message' => $message
            ]);

        Mail::assertQueued(KirimEmail::class, function ($mail) use ($recipient, $message) {
            return $mail->messageContent === $message &&
                   $mail->user->id === $recipient->id &&
                   $mail->user->email === $recipient->email &&
                   $mail->user->name === $recipient->name;
        });
    }
}
