<?php

return [

    'default' => 'bot',



    'bots' => [
        'bot' => [
            'token' => env('5202270428:AAHN1iCNFiSQC8lSQsh5yhHYQL8vOheQtVU'),
            'name' => env('UkraineSaveBot', null),
            'api_url' => 'https://api.telegram.org',
            'exceptions' => true,
            'async' => false,

            'webhook' => [
                // 'url'               => env('TELEGRAM_BOT_WEBHOOK_URL', env('APP_URL').'/telebot/webhook/bot/'.env('TELEGRAM_BOT_TOKEN')),,
                // 'certificate'       => env('TELEGRAM_BOT_CERT_PATH', storage_path('app/ssl/public.pem')),
                // 'ip_address'        => '8.8.8.8',
                // 'max_connections'   => 40,
                // 'allowed_updates'   => ["message", "edited_channel_post", "callback_query"]
            ],

            'poll' => [
                // 'limit'             => 100,
                // 'timeout'           => 0,
                // 'allowed_updates'   => ["message", "edited_channel_post", "callback_query"]
            ],

            'handlers' => [
                // Your update handlers
            ],
        ],


    ],
];
