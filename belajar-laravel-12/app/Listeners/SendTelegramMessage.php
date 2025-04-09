<?php

namespace App\Listeners;

use App\Events\UserUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class SendTelegramMessage
{
    public TelegramService $telegramService;
    /**
     * Create the event listener.
     */
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
   

    /**
     * Handle the event.
     */
    public function handle(UserUpdated $event): void
    {
        // Send a message to the user via Telegram
        if (!$event->user->telegram_id) {
            Log::info('User not have a Telegram ID.');
            return;
        }
        $user = $event->user;
        $message = "Hello {$user->name}, your profile has been updated successfully!";
        
        // Assuming you have a method to send messages via Telegram
        $this->telegramService->sendMessage($user->telegram_id, $message);
        Log::info('Telegram message sent to user: ' . $user->name);}
}
