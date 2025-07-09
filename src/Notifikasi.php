<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi;

use Rzlco\Notifikasi\Contracts\NotifikasiInterface;
use Rzlco\Notifikasi\Contracts\StorageInterface;
use Rzlco\Notifikasi\Storage\SessionStorage;
use Rzlco\Notifikasi\Storage\ArrayStorage;
use Rzlco\Notifikasi\Enums\NotificationLevel;
use Rzlco\Notifikasi\Enums\NotificationPosition;
use Rzlco\Notifikasi\Notification;

class Notifikasi implements NotifikasiInterface
{
    private StorageInterface $storage;
    private array $config;

    public function __construct(?StorageInterface $storage = null, array $config = [])
    {
        $this->storage = $storage ?? new ArrayStorage();
        
        // Handle Laravel config structure
        $resolvedConfig = $this->resolveConfig($config);
        $this->config = array_merge($this->getDefaultConfig(), $resolvedConfig);
    }

    /**
     * Resolve Laravel config structure to flat config array
     */
    private function resolveConfig(array $config): array
    {
        // If config has 'defaults' key (Laravel config structure), use it
        if (isset($config['defaults']) && is_array($config['defaults'])) {
            $resolved = $config['defaults'];
            
            // Convert position string to enum if needed
            if (isset($resolved['position']) && is_string($resolved['position'])) {
                $resolved['position'] = NotificationPosition::tryFrom($resolved['position']) 
                    ?? NotificationPosition::TOP_RIGHT;
            }
            
            // Map Laravel config keys to internal keys
            $keyMapping = [
                'max_notifications' => 'max_notifications',
                'animation_duration' => 'animation_duration',
                'closable' => 'show_close_button',
                'pause_on_hover' => 'pause_on_hover',
                'blur_strength' => 'background_blur',
                'border_radius' => 'border_radius',
                'backdrop_opacity' => 'background_opacity',
            ];
            
            foreach ($keyMapping as $laravelKey => $internalKey) {
                if (isset($resolved[$laravelKey])) {
                    $resolved[$internalKey] = $resolved[$laravelKey];
                    // Keep original key too for backward compatibility
                }
            }
            
            return $resolved;
        }
        
        // Otherwise return config as-is (direct usage)
        // But still handle position conversion if it's a string
        if (isset($config['position']) && is_string($config['position'])) {
            $config['position'] = NotificationPosition::tryFrom($config['position']) 
                ?? NotificationPosition::TOP_RIGHT;
        }
        
        return $config;
    }

    private function getDefaultConfig(): array
    {
        return [
            'position' => NotificationPosition::TOP_RIGHT,
            'duration' => 5000,
            'animation_duration' => 300,
            'max_notifications' => 5,
            'blur_effect' => true,
            'sound' => true,
            'show_close_button' => true,
            'auto_dismiss' => true,
            'rtl' => false,
            'theme' => 'auto',
            'z_index' => 999999999,
            'container_id' => 'rzlco-notifikasi-container',
            'css_prefix' => 'rzlco-notifikasi',
            'show_time' => true,
            'time_format' => '12',
            'background_opacity' => 0.85,
            'background_blur' => 25,
            'close_button_style' => 'circle',
            'border_radius' => 16,
            'min_width' => 320,
            'max_width' => 480,
            // Laravel config keys support
            'closable' => true,
            'pause_on_hover' => true,
            'blur_strength' => 25,
            'backdrop_opacity' => 0.85,
        ];
    }

    public function success(string $title, string $message = '', array $options = []): self
    {
        return $this->add(NotificationLevel::SUCCESS, $title, $message, $options);
    }

    public function error(string $title, string $message = '', array $options = []): self
    {
        return $this->add(NotificationLevel::ERROR, $title, $message, $options);
    }

    public function warning(string $title, string $message = '', array $options = []): self
    {
        return $this->add(NotificationLevel::WARNING, $title, $message, $options);
    }

    public function info(string $title, string $message = '', array $options = []): self
    {
        return $this->add(NotificationLevel::INFO, $title, $message, $options);
    }

    public function add(NotificationLevel $level, string $title, string $message = '', array $options = []): self
    {
        $notification = new Notification(
            level: $level,
            title: $title,
            message: $message,
            options: array_merge($this->config, $options)
        );

        $this->storage->add($notification);

        return $this;
    }

    public function getNotifications(): array
    {
        return $this->storage->get();
    }

    public function clear(): self
    {
        $this->storage->clear();
        return $this;
    }

    public function hasNotifications(): bool
    {
        return !empty($this->getNotifications());
    }

    public function count(): int
    {
        return count($this->getNotifications());
    }

    public function render(): string
    {
        $notifications = $this->getNotifications();

        if (empty($notifications)) {
            return '';
        }

        $html = $this->renderContainerWithNotifications($notifications);
        $html .= $this->renderStyles();
        $html .= $this->renderScript();

        // Clear notifications after rendering
        $this->clear();

        return $html;
    }

    private function renderContainerWithNotifications(array $notifications): string
    {
        $containerId = $this->config['container_id'];
        
        // Handle position whether it's string or enum
        $position = $this->config['position'];
        if ($position instanceof NotificationPosition) {
            $positionValue = $position->value;
        } else {
            $positionValue = (string) $position;
        }
        
        $zIndex = $this->config['z_index'];
        $rtl = $this->config['rtl'] ? 'rtl' : 'ltr';
        
        $notificationsHtml = $this->renderNotifications($notifications);

        return sprintf(
            '<div id="%s" class="%s-container %s-position-%s" style="z-index: %d; direction: %s;">%s</div>',
            $containerId,
            $this->config['css_prefix'],
            $this->config['css_prefix'],
            $positionValue,
            $zIndex,
            $rtl,
            $notificationsHtml
        );
    }

    private function renderContainer(): string
    {
        $containerId = $this->config['container_id'];
        
        // Handle position whether it's string or enum
        $position = $this->config['position'];
        if ($position instanceof NotificationPosition) {
            $positionValue = $position->value;
        } else {
            $positionValue = (string) $position;
        }
        
        $zIndex = $this->config['z_index'];
        $rtl = $this->config['rtl'] ? 'rtl' : 'ltr';

        return sprintf(
            '<div id="%s" class="%s-container %s-position-%s" style="z-index: %d; direction: %s;"></div>',
            $containerId,
            $this->config['css_prefix'],
            $this->config['css_prefix'],
            $positionValue,
            $zIndex,
            $rtl
        );
    }

    private function renderNotifications(array $notifications): string
    {
        $html = '';

        foreach ($notifications as $notification) {
            $html .= $this->renderNotification($notification);
        }

        return $html;
    }

    private function renderNotification(Notification $notification): string
    {
        $id = $notification->getId();
        $level = $notification->getLevel()->value;
        $title = htmlspecialchars($notification->getTitle(), ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($notification->getMessage(), ENT_QUOTES, 'UTF-8');
        $icon = $this->getIcon($notification->getLevel());
        
        // Use notification's own options (which include merged config + overrides)
        $showCloseButton = $notification->getOption('show_close_button', $this->config['show_close_button']);
        $closeButton = $showCloseButton ? $this->renderCloseButton() : '';
        $prefix = $this->config['css_prefix'];

        // Add time display if enabled in notification's options
        $timeDisplay = '';
        $showTime = $notification->getOption('show_time', $this->config['show_time']);
        if ($showTime) {
            $timeFormat = $notification->getOption('time_format', $this->config['time_format']);
            $time = $this->getCurrentTime($timeFormat);
            $timeDisplay = sprintf(
                '<div class="%s-time">%s</div>',
                $prefix,
                $time
            );
        }

        return sprintf(
            '<div id="%s" class="%s-notification %s-%s" data-level="%s" data-id="%s">
                <div class="%s-icon">%s</div>
                <div class="%s-content">
                    <div class="%s-title">%s</div>
                    %s
                </div>
                %s
                %s
            </div>',
            $id,
            $prefix,
            $prefix,
            $level,
            $level,
            $id,
            $prefix,
            $icon,
            $prefix,
            $prefix,
            $title,
            !empty($message) ? sprintf('<div class="%s-message">%s</div>', $prefix, $message) : '',
            $timeDisplay,
            $closeButton
        );
    }

    private function renderCloseButton(): string
    {
        $prefix = $this->config['css_prefix'];

        return sprintf(
            '<button class="%s-close" type="button" aria-label="Close notification">×</button>',
            $prefix
        );
    }

    private function getIcon(NotificationLevel $level): string
    {
        return match ($level) {
            NotificationLevel::SUCCESS => '✓',
            NotificationLevel::ERROR => '✕',
            NotificationLevel::WARNING => '⚠',
            NotificationLevel::INFO => 'ℹ',
        };
    }

    private function getCurrentTime(string $format = '12'): string
    {
        $now = new \DateTime();
        $hours = (int) $now->format('H');
        $minutes = (int) $now->format('i');

        if ($format === '24') {
            return sprintf('%02d:%02d', $hours, $minutes);
        } else {
            $period = $hours >= 12 ? 'PM' : 'AM';
            $displayHours = $hours % 12 ?: 12;
            return sprintf('%d:%02d %s', $displayHours, $minutes, $period);
        }
    }

    private function renderStyles(): string
    {
        $theme = $this->config['theme'] ?? 'auto';
        $backgroundOpacity = $this->config['background_opacity'] ?? 0.85;
        $backgroundBlur = $this->config['background_blur'] ?? 25;
        $borderRadius = $this->config['border_radius'] ?? 16;
        $minWidth = $this->config['min_width'] ?? 320;
        $maxWidth = $this->config['max_width'] ?? 480;
        
        // Generate theme-specific colors
        $themeStyles = $this->generateThemeStyles($theme, $backgroundOpacity);
        
        return "
        <style id=\"rzlco-notifikasi-styles\">
            .rzlco-notifikasi-container {
                position: fixed !important;
                pointer-events: none !important;
                z-index: 999999999 !important;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
                font-size: 14px !important;
                line-height: 1.4 !important;
            }
            
            .rzlco-notifikasi-position-top-right {
                top: 20px !important;
                right: 20px !important;
            }
            
            .rzlco-notifikasi-position-top-left {
                top: 20px !important;
                left: 20px !important;
            }
            
            .rzlco-notifikasi-position-bottom-right {
                bottom: 20px !important;
                right: 20px !important;
            }
            
            .rzlco-notifikasi-position-bottom-left {
                bottom: 20px !important;
                left: 20px !important;
            }
            
            .rzlco-notifikasi-position-top-center {
                top: 20px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
            }
            
            .rzlco-notifikasi-position-bottom-center {
                bottom: 20px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
            }
            
            .rzlco-notifikasi-notification {
                position: relative !important;
                display: flex !important;
                align-items: flex-start !important;
                gap: 12px !important;
                min-width: {$minWidth}px !important;
                max-width: {$maxWidth}px !important;
                padding: 16px 20px !important;
                margin-bottom: 12px !important;
                border-radius: {$borderRadius}px !important;
                backdrop-filter: blur({$backgroundBlur}px) !important;
                -webkit-backdrop-filter: blur({$backgroundBlur}px) !important;
                box-shadow: 
                    0 8px 32px rgba(0, 0, 0, 0.12),
                    0 2px 8px rgba(0, 0, 0, 0.08),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
                pointer-events: auto !important;
                cursor: default !important;
                transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
                transform: translateX(100%) scale(0.95) !important;
                opacity: 0 !important;
                overflow: hidden !important;
                {$themeStyles['notification']}
            }
            
            .rzlco-notifikasi-notification.rzlco-notifikasi-show {
                transform: translateX(0) scale(1) !important;
                opacity: 1 !important;
            }
            
            .rzlco-notifikasi-notification.rzlco-notifikasi-hide {
                transform: translateX(100%) scale(0.95) !important;
                opacity: 0 !important;
                margin-bottom: 0 !important;
                max-height: 0 !important;
                padding: 0 !important;
            }
            
            .rzlco-notifikasi-notification:hover {
                transform: translateY(-2px) scale(1.02) !important;
                box-shadow: 
                    0 12px 40px rgba(0, 0, 0, 0.15),
                    0 4px 12px rgba(0, 0, 0, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
            }
            
            .rzlco-notifikasi-icon {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 20px !important;
                height: 20px !important;
                border-radius: 50% !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                flex-shrink: 0 !important;
                margin-top: 2px !important;
                color: white !important;
            }
            
            .rzlco-notifikasi-content {
                flex: 1 !important;
                min-width: 0 !important;
            }
            
            .rzlco-notifikasi-title {
                margin: 0 0 4px 0 !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                line-height: 1.2 !important;
                {$themeStyles['text']}
            }
            
            .rzlco-notifikasi-message {
                margin: 0 !important;
                font-size: 13px !important;
                opacity: 0.8 !important;
                line-height: 1.3 !important;
                {$themeStyles['text']}
            }
            
            .rzlco-notifikasi-time {
                font-size: 11px !important;
                opacity: 0.6 !important;
                margin-right: 24px !important;
                font-weight: 500 !important;
                font-variant-numeric: tabular-nums !important;
                position: absolute !important;
                top: 12px !important;
                right: 24px !important;
                {$themeStyles['text']}
            }
            
            .rzlco-notifikasi-close {
                position: absolute !important;
                top: 12px !important;
                right: 12px !important;
                background: none !important;
                border: none !important;
                color: inherit !important;
                font-size: 12px !important;
                cursor: pointer !important;
                opacity: 0.7 !important;
                transition: all 0.2s ease !important;
                width: 20px !important;
                height: 20px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 50% !important;
                background: rgba(128, 128, 128, 0.2) !important;
                backdrop-filter: blur(10px) !important;
                -webkit-backdrop-filter: blur(10px) !important;
            }
            
            .rzlco-notifikasi-close:hover {
                opacity: 1 !important;
                background: rgba(128, 128, 128, 0.3) !important;
                transform: scale(1.1) !important;
            }
            
            .rzlco-notifikasi-close:active {
                transform: scale(0.95) !important;
            }
            
            .rzlco-notifikasi-success .rzlco-notifikasi-icon {
                background: #22c55e !important;
            }
            
            .rzlco-notifikasi-error .rzlco-notifikasi-icon {
                background: #ef4444 !important;
            }
            
            .rzlco-notifikasi-warning .rzlco-notifikasi-icon {
                background: #f59e0b !important;
            }
            
            .rzlco-notifikasi-info .rzlco-notifikasi-icon {
                background: #3b82f6 !important;
            }
            
            .rzlco-notifikasi-position-top-left .rzlco-notifikasi-notification,
            .rzlco-notifikasi-position-bottom-left .rzlco-notifikasi-notification {
                transform: translateX(-100%) scale(0.95) !important;
            }
            
            .rzlco-notifikasi-position-top-left .rzlco-notifikasi-notification.rzlco-notifikasi-show,
            .rzlco-notifikasi-position-bottom-left .rzlco-notifikasi-notification.rzlco-notifikasi-show {
                transform: translateX(0) scale(1) !important;
            }
            
            .rzlco-notifikasi-position-top-left .rzlco-notifikasi-notification.rzlco-notifikasi-hide,
            .rzlco-notifikasi-position-bottom-left .rzlco-notifikasi-notification.rzlco-notifikasi-hide {
                transform: translateX(-100%) scale(0.95) !important;
            }
            
            .rzlco-notifikasi-position-top-center .rzlco-notifikasi-notification,
            .rzlco-notifikasi-position-bottom-center .rzlco-notifikasi-notification {
                transform: translateY(-100%) scale(0.95) !important;
            }
            
            .rzlco-notifikasi-position-top-center .rzlco-notifikasi-notification.rzlco-notifikasi-show,
            .rzlco-notifikasi-position-bottom-center .rzlco-notifikasi-notification.rzlco-notifikasi-show {
                transform: translateY(0) scale(1) !important;
            }
            
            .rzlco-notifikasi-position-top-center .rzlco-notifikasi-notification.rzlco-notifikasi-hide,
            .rzlco-notifikasi-position-bottom-center .rzlco-notifikasi-notification.rzlco-notifikasi-hide {
                transform: translateY(-100%) scale(0.95) !important;
            }
            
            @media (max-width: 640px) {
                .rzlco-notifikasi-container {
                    left: 10px !important;
                    right: 10px !important;
                    transform: none !important;
                }
                
                .rzlco-notifikasi-notification {
                    min-width: auto !important;
                    max-width: none !important;
                    margin-bottom: 8px !important;
                }
            }
            
            @media (prefers-reduced-motion: reduce) {
                .rzlco-notifikasi-notification {
                    transition: none !important;
                }
            }
            
            {$themeStyles['media_query']}
        </style>";
    }

    /**
     * Generate theme-specific CSS styles
     */
    private function generateThemeStyles(string $theme, float $backgroundOpacity): array
    {
        $lightTheme = [
            'notification' => "
                background: rgba(255, 255, 255, {$backgroundOpacity}) !important;
                border: 1px solid rgba(0, 0, 0, 0.1) !important;
            ",
            'text' => "color: rgba(0, 0, 0, 0.9) !important;",
            'close' => "color: rgba(0, 0, 0, 0.9) !important;",
            'media_query' => ''
        ];
        
        $darkTheme = [
            'notification' => "
                background: rgba(30, 30, 30, {$backgroundOpacity}) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
            ",
            'text' => "color: rgba(255, 255, 255, 0.9) !important;",
            'close' => "color: rgba(255, 255, 255, 0.9) !important;",
            'media_query' => ''
        ];
        
        switch ($theme) {
            case 'light':
                return $lightTheme;
                
            case 'dark':
                return $darkTheme;
                
            case 'auto':
            default:
                // For auto theme, use CSS media query
                return [
                    'notification' => "
                        background: rgba(255, 255, 255, {$backgroundOpacity}) !important;
                        border: 1px solid rgba(0, 0, 0, 0.1) !important;
                    ",
                    'text' => "color: rgba(0, 0, 0, 0.9) !important;",
                    'close' => "color: rgba(0, 0, 0, 0.9) !important;",
                    'media_query' => "
                        @media (prefers-color-scheme: dark) {
                            .rzlco-notifikasi-notification {
                                background: rgba(30, 30, 30, {$backgroundOpacity}) !important;
                                border-color: rgba(255, 255, 255, 0.1) !important;
                            }
                            
                            .rzlco-notifikasi-title {
                                color: rgba(255, 255, 255, 0.9) !important;
                            }
                            
                            .rzlco-notifikasi-message {
                                color: rgba(255, 255, 255, 0.9) !important;
                            }
                            
                            .rzlco-notifikasi-time {
                                color: rgba(255, 255, 255, 0.9) !important;
                            }
                            
                            .rzlco-notifikasi-close {
                                color: rgba(255, 255, 255, 0.9) !important;
                            }
                        }
                    "
                ];
        }
    }

    private function renderScript(): string
    {
        $prefix = $this->config['css_prefix'];
        $duration = $this->config['duration'];
        $animationDuration = $this->config['animation_duration'];
        $autoDismiss = $this->config['auto_dismiss'] ? 'true' : 'false';
        $sound = $this->config['sound'] ? 'true' : 'false';
        $maxNotifications = $this->config['max_notifications'];

        return sprintf(
            '<script id="%s-script">%s</script>',
            $prefix,
            $this->generateScript()
        );
    }

    private function generateScript(): string
    {
        return "
            (function() {
                'use strict';
                
                const config = {
                    prefix: 'rzlco-notifikasi',
                    containerId: 'rzlco-notifikasi-container',
                    duration: {$this->config['duration']},
                    animationDuration: {$this->config['animation_duration']},
                    autoDismiss: " . ($this->config['auto_dismiss'] ? 'true' : 'false') . ",
                    sound: " . ($this->config['sound'] ? 'true' : 'false') . ",
                    maxNotifications: {$this->config['max_notifications']}
                };
                
                class NotifikasiManager {
                    constructor() {
                        this.container = null;
                        this.notifications = new Map();
                        this.init();
                    }
                    
                    init() {
                        this.container = document.getElementById(config.containerId);
                        if (!this.container) {
                            console.warn('Notifikasi container not found: ' + config.containerId);
                            return;
                        }
                        
                        console.log('NotifikasiManager initialized successfully');
                        this.setupEventListeners();
                        this.showNotifications();
                        this.limitNotifications();
                    }
                    
                    setupEventListeners() {
                        this.container.addEventListener('click', (e) => {
                            const closeBtn = e.target.closest('.' + config.prefix + '-close');
                            if (closeBtn) {
                                const notification = closeBtn.closest('.' + config.prefix + '-notification');
                                if (notification) {
                                    this.hideNotification(notification);
                                }
                            }
                        });
                        
                        this.container.addEventListener('mouseenter', (e) => {
                            const notification = e.target.closest('.' + config.prefix + '-notification');
                            if (notification && this.notifications.has(notification.id)) {
                                clearTimeout(this.notifications.get(notification.id));
                            }
                        });
                        
                        this.container.addEventListener('mouseleave', (e) => {
                            const notification = e.target.closest('.' + config.prefix + '-notification');
                            if (notification && config.autoDismiss) {
                                this.setAutoHide(notification);
                            }
                        });
                    }
                    
                    showNotifications() {
                        const notifications = this.container.querySelectorAll('.' + config.prefix + '-notification');
                        console.log('Found ' + notifications.length + ' notifications to show');
                        
                        notifications.forEach((notification, index) => {
                            setTimeout(() => {
                                this.showNotification(notification);
                            }, index * 100);
                        });
                    }
                    
                    showNotification(notification) {
                        console.log('Showing notification:', notification.id);
                        requestAnimationFrame(() => {
                            notification.classList.add(config.prefix + '-show');
                            
                            if (config.sound) {
                                this.playSound(notification.dataset.level);
                            }
                            
                            if (config.autoDismiss) {
                                this.setAutoHide(notification);
                            }
                        });
                    }
                    
                    hideNotification(notification) {
                        if (this.notifications.has(notification.id)) {
                            clearTimeout(this.notifications.get(notification.id));
                            this.notifications.delete(notification.id);
                        }
                        
                        notification.classList.add(config.prefix + '-hide');
                        
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, config.animationDuration);
                    }
                    
                    setAutoHide(notification) {
                        const timeoutId = setTimeout(() => {
                            this.hideNotification(notification);
                        }, config.duration);
                        
                        this.notifications.set(notification.id, timeoutId);
                    }
                    
                    limitNotifications() {
                        const notifications = this.container.querySelectorAll('.' + config.prefix + '-notification');
                        if (notifications.length > config.maxNotifications) {
                            for (let i = 0; i < notifications.length - config.maxNotifications; i++) {
                                this.hideNotification(notifications[i]);
                            }
                        }
                    }
                    
                    playSound(level) {
                        try {
                            const AudioContext = window.AudioContext || window.webkitAudioContext;
                            if (!AudioContext) return;

                            const audioContext = new AudioContext();
                            const oscillator = audioContext.createOscillator();
                            const gainNode = audioContext.createGain();

                            oscillator.connect(gainNode);
                            gainNode.connect(audioContext.destination);

                            const frequencies = {
                                success: 800,
                                error: 400,
                                warning: 600,
                                info: 700
                            };

                            oscillator.frequency.setValueAtTime(frequencies[level] || 700, audioContext.currentTime);
                            oscillator.type = 'sine';

                            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

                            oscillator.start(audioContext.currentTime);
                            oscillator.stop(audioContext.currentTime + 0.1);
                        } catch (error) {
                            console.warn('Could not play notification sound:', error);
                        }
                    }
                }
                
                // Initialize when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        console.log('DOM loaded, initializing NotifikasiManager');
                        new NotifikasiManager();
                    });
                } else {
                    console.log('DOM already loaded, initializing NotifikasiManager');
                    new NotifikasiManager();
                }
                
                // Fallback initialization
                setTimeout(() => {
                    if (!window.notifikasiManager) {
                        console.log('Fallback initialization of NotifikasiManager');
                        window.notifikasiManager = new NotifikasiManager();
                    }
                }, 1000);
            })();
        ";
    }
}
 