<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Storage Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default storage driver that will be used
    | to store notifications. You may set this to any of the drivers
    | defined in the "storages" array below.
    |
    | Supported: "session", "array"
    |
    */

    'default' => env('NOTIFIKASI_STORAGE', 'session'),

    /*
    |--------------------------------------------------------------------------
    | Storage Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure the storage drivers for your application's
    | notifications. Each driver has its own configuration options.
    |
    */

    'storages' => [
        'session' => [
            'driver' => 'session',
            'key' => 'rzlco_notifications',
        ],

        'array' => [
            'driver' => 'array',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Notification Settings
    |--------------------------------------------------------------------------
    |
    | These options control the default behavior of notifications.
    |
    */

    'defaults' => [
        'position' => 'top-right',
        'duration' => 5000, // milliseconds
        'theme' => 'auto', // auto, light, dark
        'sound' => true,
        'closable' => true,
        'pause_on_hover' => true,
        'rtl' => false,
        'max_notifications' => 5,
        'animation_duration' => 300, // milliseconds
        'blur_strength' => 20, // px
        'border_radius' => 16, // px
        'backdrop_opacity' => 0.8,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom CSS Classes
    |--------------------------------------------------------------------------
    |
    | You can override the default CSS classes used by the notification system.
    | This is useful if you want to integrate with your existing CSS framework.
    |
    */

    'css_classes' => [
        'container' => 'rzlco-notifikasi-container',
        'notification' => 'rzlco-notifikasi',
        'success' => 'rzlco-notifikasi-success',
        'error' => 'rzlco-notifikasi-error',
        'warning' => 'rzlco-notifikasi-warning',
        'info' => 'rzlco-notifikasi-info',
        'icon' => 'rzlco-notifikasi-icon',
        'content' => 'rzlco-notifikasi-content',
        'close' => 'rzlco-notifikasi-close',
        'progress' => 'rzlco-notifikasi-progress',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Icons
    |--------------------------------------------------------------------------
    |
    | You can override the default icons used for each notification type.
    | Use SVG strings or icon class names from your preferred icon library.
    |
    */

    'icons' => [
        'success' => '✓',
        'error' => '✕',
        'warning' => '⚠',
        'info' => 'ℹ',
        'close' => '×',
    ],

    /*
    |--------------------------------------------------------------------------
    | Apple Design System Colors
    |--------------------------------------------------------------------------
    |
    | Color palette following Apple's Human Interface Guidelines
    |
    */

    'colors' => [
        'success' => [
            'light' => '#34C759',
            'dark' => '#30D158',
        ],
        'error' => [
            'light' => '#FF3B30',
            'dark' => '#FF453A',
        ],
        'warning' => [
            'light' => '#FF9500',
            'dark' => '#FF9F0A',
        ],
        'info' => [
            'light' => '#007AFF',
            'dark' => '#0A84FF',
        ],
        'background' => [
            'light' => 'rgba(255, 255, 255, 0.8)',
            'dark' => 'rgba(28, 28, 30, 0.8)',
        ],
        'text' => [
            'light' => '#1D1D1F',
            'dark' => '#F2F2F7',
        ],
        'border' => [
            'light' => 'rgba(0, 0, 0, 0.1)',
            'dark' => 'rgba(255, 255, 255, 0.1)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sound Configuration
    |--------------------------------------------------------------------------
    |
    | Configure sound effects for notifications
    |
    */

    'sound' => [
        'enabled' => env('NOTIFIKASI_SOUND', true),
        'volume' => 0.3,
        'frequencies' => [
            'success' => [800, 1000],
            'error' => [400, 300],
            'warning' => [600, 800],
            'info' => [500, 700],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Configure performance-related settings
    |
    */

    'performance' => [
        'debounce_delay' => 100, // milliseconds
        'animation_fps' => 60,
        'lazy_load' => true,
        'preload_assets' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Settings
    |--------------------------------------------------------------------------
    |
    | Configure accessibility features
    |
    */

    'accessibility' => [
        'reduce_motion' => 'auto', // auto, always, never
        'high_contrast' => false,
        'screen_reader' => true,
        'focus_management' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | Settings for development environment
    |
    */

    'development' => [
        'debug' => env('APP_DEBUG', false),
        'log_events' => false,
        'show_performance_metrics' => false,
    ],
]; 