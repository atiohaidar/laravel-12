<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use Carbon\Carbon;

class LoginNotif extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [10, 60, 180];

    protected $loginTime;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->loginTime = Carbon::now()->format('Y-m-d H:i:s');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];
        
        if ($notifiable->telegram_id) {
            $channels[] = 'telegram';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Login Notification')
            ->view('emails.login-notification', [
                'user' => $notifiable,
                'loginTime' => $this->loginTime
            ]);
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        return TelegramMessage::create()
            ->to($notifiable->telegram_id)
            ->content("🔔 *Login Notification*\n\nHello {$notifiable->name},\nYour account was logged in at {$this->loginTime}.\n\n_If this wasn't you, please contact support immediately._")
            ->parseMode('Markdown');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'login_time' => $this->loginTime
        ];
    }
}
