<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class LoginNotif extends Notification
{
    use Queueable;

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
        return ['mail'];
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
