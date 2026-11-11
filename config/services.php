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

    'fitness_sheets' => [
        'token' => env('FITNESS_BACKUP_TOKEN'),
        'user_id' => env('FITNESS_BACKUP_USER_ID'),
        'web_app_url' => env('FITNESS_SHEETS_WEB_APP_URL'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cloud_storage' => [
        'google' => [
            'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'redirect' => env('GOOGLE_DRIVE_REDIRECT_URI'),
            'token_url' => 'https://oauth2.googleapis.com/token',
        ],
        'dropbox' => [
            'client_id' => env('DROPBOX_APP_KEY'),
            'client_secret' => env('DROPBOX_APP_SECRET'),
            'redirect' => env('DROPBOX_REDIRECT_URI'),
            'token_url' => 'https://api.dropboxapi.com/oauth2/token',
        ],
    ],

];
