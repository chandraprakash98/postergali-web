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

    'bulk_sms' => [
        'url' => env('BULK_SMS_URL', 'http://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/SendMsg'),
        'username' => env('BULK_SMS_USERNAME'),
        'password' => env('BULK_SMS_PASSWORD'),
        'sender' => env('BULK_SMS_SENDER', 'PSRGLI'),
        'timeout' => (int) env('BULK_SMS_TIMEOUT', 10),
    ],

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID', ''),
        'key_secret' => env('RAZORPAY_KEY_SECRET', ''),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET', ''),
        'api_url' => env('RAZORPAY_API_URL', 'https://api.razorpay.com/v1'),
    ],

    'apple' => [
        'bundle_id' => env('APPLE_BUNDLE_ID', 'com.postergali.postergali'),
        'key_id' => env('APPLE_KEY_ID', ''),
        'issuer_id' => env('APPLE_ISSUER_ID', ''),
        'private_key' => env('APPLE_PRIVATE_KEY', ''),
        'environment' => env('APPLE_ENVIRONMENT', 'Sandbox'),
    ],

];
