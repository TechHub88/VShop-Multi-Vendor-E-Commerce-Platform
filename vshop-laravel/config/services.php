<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY', 'rzp_live_SSn9AQrMCCO84l'),
        'secret' => env('RAZORPAY_SECRET', 'gJsZEy9b2Papg9tByq2MYMpW'),
        'mid' => env('RAZORPAY_MID', 'P4q8eE58wwC50I'),
    ],

    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY', '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'),
    ],

];
