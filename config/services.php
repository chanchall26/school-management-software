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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Providers — set SMS_PROVIDER in .env to activate
    | Supported: "fast2sms" | "msg91" | "twilio" | "none"
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'provider'  => env('SMS_PROVIDER', 'none'),
        'from_name' => env('SMS_FROM_NAME', 'Simption'),

        'fast2sms' => [
            'api_key' => env('SMS_FAST2SMS_API_KEY'),
        ],

        'msg91' => [
            'api_key'     => env('SMS_MSG91_API_KEY'),
            'sender_id'   => env('SMS_MSG91_SENDER_ID', 'SIMPTION'),
            'template_id' => env('SMS_MSG91_TEMPLATE_ID'),
        ],

        'twilio' => [
            'sid'   => env('SMS_TWILIO_SID'),
            'token' => env('SMS_TWILIO_TOKEN'),
            'from'  => env('SMS_TWILIO_FROM'),
        ],
    ],

];
