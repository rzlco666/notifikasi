@props([
    'position' => config('notifikasi.defaults.position', 'top-right'),
    'theme' => config('notifikasi.defaults.theme', 'auto'),
    'sound' => config('notifikasi.defaults.sound', true),
    'rtl' => config('notifikasi.defaults.rtl', false),
    'duration' => config('notifikasi.defaults.duration', 5000),
    'maxNotifications' => config('notifikasi.defaults.max_notifications', 5),
    'animationDuration' => config('notifikasi.defaults.animation_duration', 300),
    'blurStrength' => config('notifikasi.defaults.blur_strength', 20),
    'borderRadius' => config('notifikasi.defaults.border_radius', 16),
    'backdropOpacity' => config('notifikasi.defaults.backdrop_opacity', 0.8),
    'pauseOnHover' => config('notifikasi.defaults.pause_on_hover', true),
    'closable' => config('notifikasi.defaults.closable', true),
])

@php
    $containerClass = config('notifikasi.css_classes.container', 'rzlco-notifikasi-container');
    $notificationClass = config('notifikasi.css_classes.notification', 'rzlco-notifikasi');
    $iconClass = config('notifikasi.css_classes.icon', 'rzlco-notifikasi-icon');
    $contentClass = config('notifikasi.css_classes.content', 'rzlco-notifikasi-content');
    $closeClass = config('notifikasi.css_classes.close', 'rzlco-notifikasi-close');
    $progressClass = config('notifikasi.css_classes.progress', 'rzlco-notifikasi-progress');
    
    $colors = config('notifikasi.colors');
    $icons = config('notifikasi.icons');
    $soundConfig = config('notifikasi.sound');
    $performance = config('notifikasi.performance');
    $accessibility = config('notifikasi.accessibility');
@endphp

<div 
    id="rzlco-notifikasi-root"
    data-position="{{ $position }}"
    data-theme="{{ $theme }}"
    data-sound="{{ $sound ? 'true' : 'false' }}"
    data-rtl="{{ $rtl ? 'true' : 'false' }}"
    data-duration="{{ $duration }}"
    data-max-notifications="{{ $maxNotifications }}"
    data-animation-duration="{{ $animationDuration }}"
    data-blur-strength="{{ $blurStrength }}"
    data-border-radius="{{ $borderRadius }}"
    data-backdrop-opacity="{{ $backdropOpacity }}"
    data-pause-on-hover="{{ $pauseOnHover ? 'true' : 'false' }}"
    data-closable="{{ $closable ? 'true' : 'false' }}"
    data-sound-config="{{ json_encode($soundConfig) }}"
    data-performance-config="{{ json_encode($performance) }}"
    data-accessibility-config="{{ json_encode($accessibility) }}"
    data-colors="{{ json_encode($colors) }}"
    data-icons="{{ json_encode($icons) }}"
    class="{{ $containerClass }}"
>
    <!-- Notifications will be dynamically inserted here -->
</div>

<style>
:root {
    --rzlco-notifikasi-blur: {{ $blurStrength }}px;
    --rzlco-notifikasi-radius: {{ $borderRadius }}px;
    --rzlco-notifikasi-backdrop-opacity: {{ $backdropOpacity }};
    --rzlco-notifikasi-animation-duration: {{ $animationDuration }}ms;
    --rzlco-notifikasi-animation-easing: cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Apple Design System Colors - Light Theme */
    --rzlco-notifikasi-success-light: {{ $colors['success']['light'] }};
    --rzlco-notifikasi-error-light: {{ $colors['error']['light'] }};
    --rzlco-notifikasi-warning-light: {{ $colors['warning']['light'] }};
    --rzlco-notifikasi-info-light: {{ $colors['info']['light'] }};
    --rzlco-notifikasi-bg-light: {{ $colors['background']['light'] }};
    --rzlco-notifikasi-text-light: {{ $colors['text']['light'] }};
    --rzlco-notifikasi-border-light: {{ $colors['border']['light'] }};
    
    /* Apple Design System Colors - Dark Theme */
    --rzlco-notifikasi-success-dark: {{ $colors['success']['dark'] }};
    --rzlco-notifikasi-error-dark: {{ $colors['error']['dark'] }};
    --rzlco-notifikasi-warning-dark: {{ $colors['warning']['dark'] }};
    --rzlco-notifikasi-info-dark: {{ $colors['info']['dark'] }};
    --rzlco-notifikasi-bg-dark: {{ $colors['background']['dark'] }};
    --rzlco-notifikasi-text-dark: {{ $colors['text']['dark'] }};
    --rzlco-notifikasi-border-dark: {{ $colors['border']['dark'] }};
}

/* Auto theme detection */
@media (prefers-color-scheme: light) {
    :root {
        --rzlco-notifikasi-success: var(--rzlco-notifikasi-success-light);
        --rzlco-notifikasi-error: var(--rzlco-notifikasi-error-light);
        --rzlco-notifikasi-warning: var(--rzlco-notifikasi-warning-light);
        --rzlco-notifikasi-info: var(--rzlco-notifikasi-info-light);
        --rzlco-notifikasi-bg: var(--rzlco-notifikasi-bg-light);
        --rzlco-notifikasi-text: var(--rzlco-notifikasi-text-light);
        --rzlco-notifikasi-border: var(--rzlco-notifikasi-border-light);
    }
}

@media (prefers-color-scheme: dark) {
    :root {
        --rzlco-notifikasi-success: var(--rzlco-notifikasi-success-dark);
        --rzlco-notifikasi-error: var(--rzlco-notifikasi-error-dark);
        --rzlco-notifikasi-warning: var(--rzlco-notifikasi-warning-dark);
        --rzlco-notifikasi-info: var(--rzlco-notifikasi-info-dark);
        --rzlco-notifikasi-bg: var(--rzlco-notifikasi-bg-dark);
        --rzlco-notifikasi-text: var(--rzlco-notifikasi-text-dark);
        --rzlco-notifikasi-border: var(--rzlco-notifikasi-border-dark);
    }
}

/* Light theme override */
[data-theme="light"] {
    --rzlco-notifikasi-success: var(--rzlco-notifikasi-success-light);
    --rzlco-notifikasi-error: var(--rzlco-notifikasi-error-light);
    --rzlco-notifikasi-warning: var(--rzlco-notifikasi-warning-light);
    --rzlco-notifikasi-info: var(--rzlco-notifikasi-info-light);
    --rzlco-notifikasi-bg: var(--rzlco-notifikasi-bg-light);
    --rzlco-notifikasi-text: var(--rzlco-notifikasi-text-light);
    --rzlco-notifikasi-border: var(--rzlco-notifikasi-border-light);
}

/* Dark theme override */
[data-theme="dark"] {
    --rzlco-notifikasi-success: var(--rzlco-notifikasi-success-dark);
    --rzlco-notifikasi-error: var(--rzlco-notifikasi-error-dark);
    --rzlco-notifikasi-warning: var(--rzlco-notifikasi-warning-dark);
    --rzlco-notifikasi-info: var(--rzlco-notifikasi-info-dark);
    --rzlco-notifikasi-bg: var(--rzlco-notifikasi-bg-dark);
    --rzlco-notifikasi-text: var(--rzlco-notifikasi-text-dark);
    --rzlco-notifikasi-border: var(--rzlco-notifikasi-border-dark);
}

/* Container positioning */
.{{ $containerClass }} {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    font-size: 14px;
    line-height: 1.4;
}

/* Position variants */
.{{ $containerClass }}[data-position="top-left"] {
    top: 20px;
    left: 20px;
}

.{{ $containerClass }}[data-position="top-center"] {
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
}

.{{ $containerClass }}[data-position="top-right"] {
    top: 20px;
    right: 20px;
}

.{{ $containerClass }}[data-position="bottom-left"] {
    bottom: 20px;
    left: 20px;
}

.{{ $containerClass }}[data-position="bottom-center"] {
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
}

.{{ $containerClass }}[data-position="bottom-right"] {
    bottom: 20px;
    right: 20px;
}

/* Notification base styles */
.{{ $notificationClass }} {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    min-width: 320px;
    max-width: 480px;
    padding: 16px 20px;
    margin-bottom: 12px;
    background: var(--rzlco-notifikasi-bg);
    border: 1px solid var(--rzlco-notifikasi-border);
    border-radius: var(--rzlco-notifikasi-radius);
    backdrop-filter: blur(var(--rzlco-notifikasi-blur));
    -webkit-backdrop-filter: blur(var(--rzlco-notifikasi-blur));
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.12),
        0 2px 8px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    color: var(--rzlco-notifikasi-text);
    pointer-events: auto;
    cursor: default;
    transition: all var(--rzlco-notifikasi-animation-duration) var(--rzlco-notifikasi-animation-easing);
    transform: translateX(0);
    opacity: 1;
}

/* RTL support */
.{{ $containerClass }}[data-rtl="true"] .{{ $notificationClass }} {
    direction: rtl;
}

/* Hover effects */
.{{ $notificationClass }}:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 4px 12px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

/* Icon styles */
.{{ $iconClass }} {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
    margin-top: 2px;
}

/* Content styles */
.{{ $contentClass }} {
    flex: 1;
    min-width: 0;
}

.{{ $contentClass }} h4 {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.2;
}

.{{ $contentClass }} p {
    margin: 0;
    font-size: 13px;
    opacity: 0.8;
    line-height: 1.3;
}

/* Close button */
.{{ $closeClass }} {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border: none;
    background: none;
    color: inherit;
    font-size: 16px;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s ease;
    flex-shrink: 0;
    margin-top: 2px;
}

.{{ $closeClass }}:hover {
    opacity: 1;
}

/* Progress bar */
.{{ $progressClass }} {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: currentColor;
    border-radius: 0 0 var(--rzlco-notifikasi-radius) var(--rzlco-notifikasi-radius);
    opacity: 0.3;
    transition: width linear;
}

/* Type-specific styles */
.{{ $notificationClass }}.{{ config('notifikasi.css_classes.success') }} {
    --current-color: var(--rzlco-notifikasi-success);
}

.{{ $notificationClass }}.{{ config('notifikasi.css_classes.error') }} {
    --current-color: var(--rzlco-notifikasi-error);
}

.{{ $notificationClass }}.{{ config('notifikasi.css_classes.warning') }} {
    --current-color: var(--rzlco-notifikasi-warning);
}

.{{ $notificationClass }}.{{ config('notifikasi.css_classes.info') }} {
    --current-color: var(--rzlco-notifikasi-info);
}

.{{ $notificationClass }} .{{ $iconClass }} {
    background: var(--current-color);
    color: white;
}

.{{ $notificationClass }} .{{ $progressClass }} {
    background: var(--current-color);
}

/* Animation states */
.{{ $notificationClass }}.rzlco-notifikasi-enter {
    opacity: 0;
    transform: translateX(100%);
}

.{{ $notificationClass }}.rzlco-notifikasi-enter-active {
    opacity: 1;
    transform: translateX(0);
}

.{{ $notificationClass }}.rzlco-notifikasi-exit {
    opacity: 1;
    transform: translateX(0);
}

.{{ $notificationClass }}.rzlco-notifikasi-exit-active {
    opacity: 0;
    transform: translateX(100%);
}

/* Left-positioned animations */
.{{ $containerClass }}[data-position*="left"] .{{ $notificationClass }}.rzlco-notifikasi-enter {
    transform: translateX(-100%);
}

.{{ $containerClass }}[data-position*="left"] .{{ $notificationClass }}.rzlco-notifikasi-exit-active {
    transform: translateX(-100%);
}

/* Center-positioned animations */
.{{ $containerClass }}[data-position*="center"] .{{ $notificationClass }}.rzlco-notifikasi-enter {
    transform: translateY(-100%);
}

.{{ $containerClass }}[data-position*="center"] .{{ $notificationClass }}.rzlco-notifikasi-exit-active {
    transform: translateY(-100%);
}

/* Mobile responsive */
@media (max-width: 640px) {
    .{{ $containerClass }} {
        left: 10px !important;
        right: 10px !important;
        top: 10px !important;
        bottom: 10px !important;
        transform: none !important;
    }
    
    .{{ $notificationClass }} {
        min-width: auto;
        max-width: none;
        margin-bottom: 8px;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .{{ $notificationClass }} {
        transition: none;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .{{ $notificationClass }} {
        border-width: 2px;
        box-shadow: none;
    }
}
</style>

<script>
// Initialize notification system when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.RzlcoNotifikasi === 'undefined') {
        console.warn('RzlcoNotifikasi not found. Make sure to include the notification script.');
        return;
    }
    
    const container = document.getElementById('rzlco-notifikasi-root');
    if (!container) {
        console.warn('Notification container not found.');
        return;
    }
    
    // Initialize with container configuration
    const config = {
        position: container.dataset.position,
        theme: container.dataset.theme,
        sound: container.dataset.sound === 'true',
        rtl: container.dataset.rtl === 'true',
        duration: parseInt(container.dataset.duration),
        maxNotifications: parseInt(container.dataset.maxNotifications),
        animationDuration: parseInt(container.dataset.animationDuration),
        blurStrength: parseInt(container.dataset.blurStrength),
        borderRadius: parseInt(container.dataset.borderRadius),
        backdropOpacity: parseFloat(container.dataset.backdropOpacity),
        pauseOnHover: container.dataset.pauseOnHover === 'true',
        closable: container.dataset.closable === 'true',
        soundConfig: JSON.parse(container.dataset.soundConfig || '{}'),
        performance: JSON.parse(container.dataset.performanceConfig || '{}'),
        accessibility: JSON.parse(container.dataset.accessibilityConfig || '{}'),
        colors: JSON.parse(container.dataset.colors || '{}'),
        icons: JSON.parse(container.dataset.icons || '{}')
    };
    
    // Initialize the notification system
    window.RzlcoNotifikasi.init(config);
    
    // Expose Laravel helper methods
    window.notifikasi = {
        success: (title, message, options = {}) => window.RzlcoNotifikasi.success(title, message, options),
        error: (title, message, options = {}) => window.RzlcoNotifikasi.error(title, message, options),
        warning: (title, message, options = {}) => window.RzlcoNotifikasi.warning(title, message, options),
        info: (title, message, options = {}) => window.RzlcoNotifikasi.info(title, message, options),
        clear: () => window.RzlcoNotifikasi.clear(),
        clearAll: () => window.RzlcoNotifikasi.clearAll()
    };
});
</script> 