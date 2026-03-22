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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Face Attendance API
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the Face Attendance system API
    | to retrieve attendance reports for employees.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot
    |--------------------------------------------------------------------------
    |
    | Configuration for the Telegram Bot used for leave/approval notifications,
    | quick reply approve/reject buttons, and daily summary messages.
    |
    */

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET', 'telegram-webhook-secret'),
    ],

    'face_attendance' => [
        'base_url' => env('FACE_ATTENDANCE_API_URL', 'https://nass.ac.th/faceattendance/api/v1/reports'),
        'api_key' => env('FACE_ATTENDANCE_API_KEY'),
        // URL สำหรับดึงรูปภาพจาก Face Attendance storage
        'storage_url' => env('FACE_ATTENDANCE_STORAGE_URL', 'https://nass.ac.th/faceattendance/storage'),
    ],

];
