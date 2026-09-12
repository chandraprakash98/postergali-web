<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Daily Poster Limit Per Phone Number
    |--------------------------------------------------------------------------
    |
    | Defines the maximum number of posters (jobs and offers) that can be
    | posted using a single phone/mobile number within one day.
    | Set to 0 or null to disable the limit (unlimited).
    |
    */

    'daily_limit_per_phone' => (int) env('MAX_POSTERS_PER_DAY_PER_PHONE', 2),

    /*
    |--------------------------------------------------------------------------
    | Limit Mode
    |--------------------------------------------------------------------------
    |
    | 'combined': Total sum of jobs + offers posted today cannot exceed limit.
    | 'per_type': Jobs and offers are counted separately up to the limit each.
    |
    */

    'limit_mode' => env('POSTER_DAILY_LIMIT_MODE', 'combined'),

    /*
    |--------------------------------------------------------------------------
    | Daily Limit Error Message
    |--------------------------------------------------------------------------
    |
    | The error message thrown when the limit is exceeded.
    | The ':limit' placeholder will be replaced with the numeric limit.
    |
    */

    'error_message' => env(
        'POSTER_DAILY_LIMIT_MESSAGE',
        'Daily poster limit reached. You can only post up to :limit posters per day.'
    ),

    /*
    |--------------------------------------------------------------------------
    | Poster Expiry Notification Batch Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the automated batch job that checks for expired/expiring
    | posters and dispatches multilingual Firebase FCM push notifications.
    |
    */

    'expiry_notification' => [
        // Cron schedule expression (Default: every 15 minutes)
        'schedule' => env('POSTER_EXPIRY_CRON_SCHEDULE', '*/15 * * * *'),

        // Number of hours ahead to detect posters expiring tomorrow (Default: 24 hours)
        'window_hours' => (int) env('POSTER_EXPIRING_WINDOW_HOURS', 24),

        // Master toggle for automated expiry notifications
        'enabled' => (bool) env('POSTER_EXPIRY_NOTIFICATION_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | 1. Ad View Milestone Notifications (100, 200, 300...)
    |--------------------------------------------------------------------------
    |
    | Triggered when a poster crosses view thresholds (e.g. 100, 200, 300).
    |
    */

    'view_milestones' => [
        'thresholds' => [100, 200, 300, 400, 500, 1000],

        'title' => env(
            'POSTER_VIEW_MILESTONE_TITLE',
            '🎉 Views Milestone Crossed! | व्यूज माइलस्टोन सूचना'
        ),

        'body_en' => env(
            'POSTER_VIEW_MILESTONE_BODY_EN',
            '🚀 Great news! Your poster ":business_name" has crossed :views views on PosterGali! Keep tracking your responses.'
        ),

        'body_hi' => env(
            'POSTER_VIEW_MILESTONE_BODY_HI',
            '🚀 खुशखबरी! PosterGali पर आपके पोस्टर ":business_name" ने :views व्यूज पूरे कर लिए हैं! नए ग्राहकों से जुड़े रहें।'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | 2. On Poster Expiry Notification (Batch)
    |--------------------------------------------------------------------------
    |
    | Triggered by the 15-min batch when a poster has reached its expiry date.
    |
    */

    'on_expiry_notification' => [
        'title' => env(
            'POSTER_ON_EXPIRY_NOTIFICATION_TITLE',
            '⚠️ Poster Expired | पोस्टर समाप्त हो गया'
        ),

        'body_en' => env(
            'POSTER_ON_EXPIRY_BODY_EN',
            '⚠️ Your poster ":business_name" has expired today. Create a new poster on PosterGali and keep reaching more people online.'
        ),

        'body_hi' => env(
            'POSTER_ON_EXPIRY_BODY_HI',
            '⚠️ आपके पोस्टर ":business_name" की अवधि आज समाप्त हो गई है। नया पोस्टर बनाएं और PosterGali पर अधिक लोगों तक पहुंचना जारी रखें।'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | 3. Day Before Poster Expiry Notification (Batch)
    |--------------------------------------------------------------------------
    |
    | Triggered by the 15-min batch 1 day (within 24 hours) before expiry.
    |
    */

    'day_before_expiry_notification' => [
        'title' => env(
            'POSTER_DAY_BEFORE_NOTIFICATION_TITLE',
            '⏰ Poster Expires Tomorrow | पोस्टर कल समाप्त हो रहा है'
        ),

        'body_en' => env(
            'POSTER_DAY_BEFORE_BODY_EN',
            '⏰ Reminder: Your poster ":business_name" expires tomorrow! Create a new poster on PosterGali to keep your business live without interruption.'
        ),

        'body_hi' => env(
            'POSTER_DAY_BEFORE_BODY_HI',
            '⏰ सूचना: आपका पोस्टर ":business_name" कल समाप्त हो रहा है! अपना प्रचार बिना रुके जारी रखने के लिए PosterGali पर नया पोस्टर बनाएं।'
        ),
    ],

];
