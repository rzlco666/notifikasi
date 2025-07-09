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
    | These options control the default behavior and appearance of notifications.
    | All settings can be overridden per notification if needed.
    |
    */

    'defaults' => [
        // Position: 'top-right', 'top-left', 'top-center', 'bottom-right', 'bottom-left', 'bottom-center'
        'position' => env('NOTIFIKASI_POSITION', 'top-right'),
        
        // Duration in milliseconds (0 = no auto dismiss)
        'duration' => (int) env('NOTIFIKASI_DURATION', 5000),
        
        // Theme: 'auto', 'light', 'dark'
        'theme' => env('NOTIFIKASI_THEME', 'auto'),
        
        // Enable sound effects
        'sound' => (bool) env('NOTIFIKASI_SOUND', true),
        
        // Show close button
        'closable' => (bool) env('NOTIFIKASI_CLOSABLE', true),
        
        // Pause timer on hover
        'pause_on_hover' => (bool) env('NOTIFIKASI_PAUSE_ON_HOVER', true),
        
        // RTL support
        'rtl' => (bool) env('NOTIFIKASI_RTL', false),
        
        // Maximum notifications to show at once
        'max_notifications' => (int) env('NOTIFIKASI_MAX_NOTIFICATIONS', 5),
        
        // Animation duration in milliseconds
        'animation_duration' => (int) env('NOTIFIKASI_ANIMATION_DURATION', 300),
        
        // Background blur strength in pixels
        'blur_strength' => (int) env('NOTIFIKASI_BLUR_STRENGTH', 10),
        
        // Border radius in pixels
        'border_radius' => (int) env('NOTIFIKASI_BORDER_RADIUS', 16),
        
        // Background opacity (0.0 - 1.0)
        'backdrop_opacity' => (float) env('NOTIFIKASI_BACKDROP_OPACITY', 0.50),
        
        // Show time display
        'show_time' => (bool) env('NOTIFIKASI_SHOW_TIME', true),
        
        // Time format: '12' or '24'
        'time_format' => env('NOTIFIKASI_TIME_FORMAT', '12'),
        
        // Auto dismiss notifications
        'auto_dismiss' => (bool) env('NOTIFIKASI_AUTO_DISMISS', true),
        
        // Minimum width in pixels
        'min_width' => (int) env('NOTIFIKASI_MIN_WIDTH', 320),
        
        // Maximum width in pixels
        'max_width' => (int) env('NOTIFIKASI_MAX_WIDTH', 480),
        
        // Z-index for notifications
        'z_index' => (int) env('NOTIFIKASI_Z_INDEX', 999999999),
        
        // Close button style: 'circle', 'minimal'
        'close_button_style' => env('NOTIFIKASI_CLOSE_BUTTON_STYLE', 'circle'),
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
        'title' => 'rzlco-notifikasi-title',
        'message' => 'rzlco-notifikasi-message',
        'close' => 'rzlco-notifikasi-close',
        'time' => 'rzlco-notifikasi-time',
        'progress' => 'rzlco-notifikasi-progress',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Icons
    |--------------------------------------------------------------------------
    |
    | You can override the default icons used for each notification type.
    | Use Unicode characters, SVG strings, or icon class names from your
    | preferred icon library.
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
    | Color palette following Apple's Human Interface Guidelines.
    | These colors are used for icons and accents.
    |
    */

    'colors' => [
        'success' => [
            'light' => '#22c55e',
            'dark' => '#34c759',
        ],
        'error' => [
            'light' => '#ef4444',
            'dark' => '#ff453a',
        ],
        'warning' => [
            'light' => '#f59e0b',
            'dark' => '#ff9f0a',
        ],
        'info' => [
            'light' => '#3b82f6',
            'dark' => '#0a84ff',
        ],
        'background' => [
            'light' => 'rgba(255, 255, 255, 0.85)',
            'dark' => 'rgba(30, 30, 30, 0.85)',
        ],
        'text' => [
            'light' => 'rgba(0, 0, 0, 0.9)',
            'dark' => 'rgba(255, 255, 255, 0.9)',
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
    | Configure sound effects for notifications. Sounds are generated using
    | Web Audio API with different frequencies for each notification type.
    |
    */

    'sound' => [
        'enabled' => (bool) env('NOTIFIKASI_SOUND_ENABLED', true),
        'volume' => (float) env('NOTIFIKASI_SOUND_VOLUME', 0.1),
        'frequencies' => [
            'success' => 800,
            'error' => 400,
            'warning' => 600,
            'info' => 700,
        ],
        'duration' => 100, // milliseconds
        'type' => 'sine', // 'sine', 'square', 'sawtooth', 'triangle'
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Configure performance-related settings for optimal user experience.
    |
    */

    'performance' => [
        'debounce_delay' => 100, // milliseconds
        'animation_fps' => 60,
        'lazy_load' => true,
        'preload_assets' => false,
        'stagger_delay' => 100, // delay between showing multiple notifications
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Settings
    |--------------------------------------------------------------------------
    |
    | Configure accessibility features to ensure your notifications are
    | usable by everyone, including users with disabilities.
    |
    */

    'accessibility' => [
        'reduce_motion' => 'auto', // 'auto', 'always', 'never'
        'high_contrast' => false,
        'screen_reader' => true,
        'focus_management' => true,
        'keyboard_navigation' => true,
        'aria_live' => 'polite', // 'polite', 'assertive', 'off'
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile Responsive Settings
    |--------------------------------------------------------------------------
    |
    | Configure how notifications behave on mobile devices.
    |
    */

    'mobile' => [
        'breakpoint' => 640, // pixels
        'full_width' => true,
        'margin' => 10, // pixels
        'stacking' => 'vertical', // 'vertical', 'overlay'
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | Settings useful during development and debugging.
    |
    */

    'development' => [
        'debug' => (bool) env('NOTIFIKASI_DEBUG', false),
        'log_events' => (bool) env('NOTIFIKASI_LOG_EVENTS', false),
        'show_performance_metrics' => (bool) env('NOTIFIKASI_SHOW_PERFORMANCE', false),
        'console_output' => (bool) env('NOTIFIKASI_CONSOLE_OUTPUT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Advanced Configuration
    |--------------------------------------------------------------------------
    |
    | Advanced settings for power users and custom implementations.
    |
    */

    'advanced' => [
        'container_id' => 'rzlco-notifikasi-container',
        'css_prefix' => 'rzlco-notifikasi',
        'custom_css_url' => null,
        'custom_js_url' => null,
        'inline_styles' => true,
        'inline_scripts' => true,
        'escape_html' => true,
        'allow_html' => false,
    ],
]; 