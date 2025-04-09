<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\LoginNotif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        
        // Set fake Telegram token for testing
        config(['services.telegram.bot.token' => 'test-token']);
    }

    public function test_user_without_telegram_id_only_receives_email_notification()
    {
        Notification::fake();

        $user = User::factory()->create([
            'telegram_id' => null
        ]);

        $user->notify(new LoginNotif());

        Notification::assertSentTo(
            $user,
            LoginNotif::class,
            function ($notification, $channels) {
                return in_array('mail', $channels) 
                    && !in_array('telegram', $channels);
            }
        );
    }

    public function test_user_with_telegram_id_receives_both_notifications()
    {
        Notification::fake();

        $user = User::factory()->create([
            'telegram_id' => '123456789'
        ]);

        $user->notify(new LoginNotif());

        Notification::assertSentTo(
            $user,
            LoginNotif::class,
            function ($notification, $channels) {
                return in_array('mail', $channels) 
                    && in_array('telegram', $channels);
            }
        );
    }

    public function test_telegram_notification_has_correct_format()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'telegram_id' => '123456789'
        ]);

        $notification = new LoginNotif();
        $telegramMessage = $notification->toTelegram($user);

        $this->assertInstanceOf(TelegramMessage::class, $telegramMessage);
        
        // Get the message payload and verify its contents
        $messageData = $telegramMessage->toArray();
        $this->assertStringContainsString('Test User', $messageData['text']);
        $this->assertStringContainsString('Login Notification', $messageData['text']);
        $this->assertEquals('123456789', $messageData['chat_id']);
    }

    public function test_user_can_update_telegram_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put("/users/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'telegram_id' => '987654321'
            ]);

        $response->assertRedirect();
        $this->assertEquals('987654321', $user->fresh()->telegram_id);
    }

    public function test_telegram_id_is_optional()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->put("/users/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'telegram_id' => null
            ]);

        $response->assertRedirect();
        $this->assertNull($user->fresh()->telegram_id);
    }
}
