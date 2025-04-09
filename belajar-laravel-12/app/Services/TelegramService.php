<?php

namespace App\Services;

use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

class TelegramService
{
    protected $token;
    protected $client;

    public function __construct()
    {
        $this->token = Config::get('services.telegram-bot-api.token');
        $this->client = new Client();
        echo "Telegram Service Initialized with Token: {$this->token}\n";
    }

    public function sendMessage(string $chatId, string $message)
    {
        

        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $response = $this->client->post($url, [
            'form_params' => [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
