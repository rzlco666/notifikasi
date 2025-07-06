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
        $this->config = array_merge($this->getDefaultConfig(), $config);
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
            'theme' => 'light',
            'z_index' => 999999,
            'container_id' => 'rzlco-notifikasi-container',
            'css_prefix' => 'rzlco-notifikasi',
            'show_time' => true,
            'time_format' => '12',
            'background_opacity' => 0.85,
            'background_blur' => 25,
            'close_button_style' => 'circle',
        ];
    }

    public function success(string $message, array $options = []): self
    {
        return $this->add(NotificationLevel::SUCCESS, $message, $options);
    }

    public function error(string $message, array $options = []): self
    {
        return $this->add(NotificationLevel::ERROR, $message, $options);
    }

    public function warning(string $message, array $options = []): self
    {
        return $this->add(NotificationLevel::WARNING, $message, $options);
    }

    public function info(string $message, array $options = []): self
    {
        return $this->add(NotificationLevel::INFO, $message, $options);
    }

    public function add(NotificationLevel $level, string $message, array $options = []): self
    {
        $notification = new Notification(
            level: $level,
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

        $html = $this->renderContainer();
        $html .= $this->renderNotifications($notifications);
        $html .= $this->renderStyles();
        $html .= $this->renderScript();

        // Clear notifications after rendering
        $this->clear();

        return $html;
    }

    private function renderContainer(): string
    {
        $containerId = $this->config['container_id'];
        $position = $this->config['position']->value;
        $zIndex = $this->config['z_index'];
        $rtl = $this->config['rtl'] ? 'rtl' : 'ltr';

        return sprintf(
            '<div id="%s" class="%s-container %s-position-%s" style="z-index: %d; direction: %s;"></div>',
            $containerId,
            $this->config['css_prefix'],
            $this->config['css_prefix'],
            $position,
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
        $message = htmlspecialchars($notification->getMessage(), ENT_QUOTES, 'UTF-8');
        $icon = $this->getIcon($notification->getLevel());
        $closeButton = $this->config['show_close_button'] ? $this->renderCloseButton() : '';
        $prefix = $this->config['css_prefix'];

        // Add time display if enabled
        $timeDisplay = '';
        if ($this->config['show_time']) {
            $time = $this->getCurrentTime();
            $timeDisplay = sprintf(
                '<div class="%s-time">%s</div>',
                $prefix,
                $time
            );
        }

        return sprintf(
            '<div id="%s" class="%s-notification %s-level-%s" data-level="%s" data-id="%s">
                <div class="%s-icon">%s</div>
                <div class="%s-content">
                    <div class="%s-message">%s</div>
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
            $message,
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
            NotificationLevel::SUCCESS => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.5 4.5L6 12L2.5 8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>',
            NotificationLevel::ERROR => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>',
            NotificationLevel::WARNING => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 1L15 14H1L8 1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 5V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 13H8.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>',
            NotificationLevel::INFO => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2"/>
                <path d="M8 11V8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 5H8.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>',
        };
    }

    private function getCurrentTime(): string
    {
        $now = new \DateTime();
        $hours = (int) $now->format('H');
        $minutes = (int) $now->format('i');

        if ($this->config['time_format'] === '24') {
            return sprintf('%02d:%02d', $hours, $minutes);
        } else {
            $period = $hours >= 12 ? 'PM' : 'AM';
            $displayHours = $hours % 12 ?: 12;
            return sprintf('%d:%02d %s', $displayHours, $minutes, $period);
        }
    }

    private function renderStyles(): string
    {
        $prefix = $this->config['css_prefix'];
        $theme = $this->config['theme'];
        $animationDuration = $this->config['animation_duration'];
        $blurEffect = $this->config['blur_effect'] ? 'backdrop-filter: blur(20px);' : '';

        return sprintf(
            '<style id="%s-styles">%s</style>',
            $prefix,
            $this->getCssStyles($prefix, $theme, $animationDuration, $blurEffect)
        );
    }

    private function getCssStyles(string $prefix, string $theme, int $animationDuration, string $blurEffect): string
    {
        $colors = $this->getThemeColors($theme);
        $backgroundOpacity = $this->config['background_opacity'];
        $backgroundBlur = $this->config['background_blur'];

        return "
            .{$prefix}-container {
                position: fixed;
                pointer-events: none;
                z-index: {$this->config['z_index']};
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                font-size: 14px;
                line-height: 1.4;
            }
            
            .{$prefix}-position-top-right {
                top: 20px;
                right: 20px;
            }
            
            .{$prefix}-position-top-left {
                top: 20px;
                left: 20px;
            }
            
            .{$prefix}-position-bottom-right {
                bottom: 20px;
                right: 20px;
            }
            
            .{$prefix}-position-bottom-left {
                bottom: 20px;
                left: 20px;
            }
            
            .{$prefix}-position-top-center {
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
            }
            
            .{$prefix}-position-bottom-center {
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
            }
            
            .{$prefix}-notification {
                pointer-events: auto;
                margin-bottom: 12px;
                min-width: 300px;
                max-width: 500px;
                border-radius: 16px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                background: {$colors['background']};
                border: 1px solid {$colors['border']};
                backdrop-filter: blur({$backgroundBlur}px);
                -webkit-backdrop-filter: blur({$backgroundBlur}px);
                opacity: 0;
                transform: translateX(100%) scale(0.95);
                transition: all {$animationDuration}ms cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
                position: relative;
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 16px 20px;
            }
            
            .{$prefix}-notification.{$prefix}-show {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
            
            .{$prefix}-notification.{$prefix}-hide {
                opacity: 0;
                transform: translateX(100%) scale(0.95);
                margin-bottom: 0;
                max-height: 0;
                padding: 0;
            }
            
            .{$prefix}-notification:hover {
                transform: translateY(-2px) scale(1.02);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
            }
            
            .{$prefix}-content {
                flex: 1;
                min-width: 0;
            }
            
            .{$prefix}-icon {
                flex-shrink: 0;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-top: 1px;
            }
            
            .{$prefix}-message {
                color: {$colors['text']};
                font-weight: 500;
                word-break: break-word;
                margin: 0;
            }
            
            .{$prefix}-time {
                font-size: 11px;
                opacity: 0.6;
                margin-right: 24px;
                font-weight: 500;
                font-variant-numeric: tabular-nums;
                color: {$colors['text']};
                position: absolute;
                top: 12px;
                right: 24px;
            }
            
            .{$prefix}-close {
                position: absolute;
                top: 12px;
                right: 12px;
                background: rgba(128, 128, 128, 0.2);
                border: none;
                cursor: pointer;
                padding: 4px;
                border-radius: 50%;
                color: {$colors['text']};
                opacity: 0.7;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 20px;
                height: 20px;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                font-size: 12px;
            }
            
            .{$prefix}-close:hover {
                opacity: 1;
                background: rgba(128, 128, 128, 0.3);
                transform: scale(1.1);
            }
            
            .{$prefix}-close:active {
                transform: scale(0.95);
            }
            
            .{$prefix}-level-success {
                background: {$colors['success_bg']};
                border-color: {$colors['success_border']};
            }
            
            .{$prefix}-level-success .{$prefix}-icon {
                color: {$colors['success_icon']};
            }
            
            .{$prefix}-level-error {
                background: {$colors['error_bg']};
                border-color: {$colors['error_border']};
            }
            
            .{$prefix}-level-error .{$prefix}-icon {
                color: {$colors['error_icon']};
            }
            
            .{$prefix}-level-warning {
                background: {$colors['warning_bg']};
                border-color: {$colors['warning_border']};
            }
            
            .{$prefix}-level-warning .{$prefix}-icon {
                color: {$colors['warning_icon']};
            }
            
            .{$prefix}-level-info {
                background: {$colors['info_bg']};
                border-color: {$colors['info_border']};
            }
            
            .{$prefix}-level-info .{$prefix}-icon {
                color: {$colors['info_icon']};
            }
            
            .{$prefix}-container-left .{$prefix}-notification {
                transform: translateX(-100%) scale(0.95);
            }
            
            .{$prefix}-container-left .{$prefix}-notification.{$prefix}-show {
                transform: translateX(0) scale(1);
            }
            
            .{$prefix}-container-left .{$prefix}-notification.{$prefix}-hide {
                transform: translateX(-100%) scale(0.95);
            }
            
            .{$prefix}-container-center .{$prefix}-notification {
                transform: translateY(-100%) scale(0.95);
            }
            
            .{$prefix}-container-center .{$prefix}-notification.{$prefix}-show {
                transform: translateY(0) scale(1);
            }
            
            .{$prefix}-container-center .{$prefix}-notification.{$prefix}-hide {
                transform: translateY(-100%) scale(0.95);
            }
            
            @media (max-width: 640px) {
                .{$prefix}-container {
                    left: 10px !important;
                    right: 10px !important;
                    transform: none !important;
                }
                
                .{$prefix}-notification {
                    min-width: auto;
                    max-width: none;
                    margin-left: 0;
                    margin-right: 0;
                }
            }
            
            @media (prefers-reduced-motion: reduce) {
                .{$prefix}-notification {
                    transition: none;
                }
            }
        ";
    }

    private function getThemeColors(string $theme): array
    {
        $backgroundOpacity = $this->config['background_opacity'];

        return match ($theme) {
            'dark' => [
                'background' => "rgba(30, 30, 30, {$backgroundOpacity})",
                'border' => 'rgba(255, 255, 255, 0.1)',
                'text' => 'rgba(255, 255, 255, 0.9)',
                'close_hover' => 'rgba(255, 255, 255, 0.1)',
                'success_bg' => 'rgba(34, 197, 94, 0.1)',
                'success_border' => 'rgba(34, 197, 94, 0.2)',
                'success_icon' => '#22c55e',
                'error_bg' => 'rgba(239, 68, 68, 0.1)',
                'error_border' => 'rgba(239, 68, 68, 0.2)',
                'error_icon' => '#ef4444',
                'warning_bg' => 'rgba(245, 158, 11, 0.1)',
                'warning_border' => 'rgba(245, 158, 11, 0.2)',
                'warning_icon' => '#f59e0b',
                'info_bg' => 'rgba(59, 130, 246, 0.1)',
                'info_border' => 'rgba(59, 130, 246, 0.2)',
                'info_icon' => '#3b82f6',
            ],
            default => [
                'background' => "rgba(255, 255, 255, {$backgroundOpacity})",
                'border' => 'rgba(0, 0, 0, 0.1)',
                'text' => 'rgba(0, 0, 0, 0.9)',
                'close_hover' => 'rgba(0, 0, 0, 0.05)',
                'success_bg' => 'rgba(34, 197, 94, 0.05)',
                'success_border' => 'rgba(34, 197, 94, 0.2)',
                'success_icon' => '#16a34a',
                'error_bg' => 'rgba(239, 68, 68, 0.05)',
                'error_border' => 'rgba(239, 68, 68, 0.2)',
                'error_icon' => '#dc2626',
                'warning_bg' => 'rgba(245, 158, 11, 0.05)',
                'warning_border' => 'rgba(245, 158, 11, 0.2)',
                'warning_icon' => '#d97706',
                'info_bg' => 'rgba(59, 130, 246, 0.05)',
                'info_border' => 'rgba(59, 130, 246, 0.2)',
                'info_icon' => '#2563eb',
            ],
        };
    }

    private function renderScript(): string
    {
        $prefix = $this->config['css_prefix'];
        $containerId = $this->config['container_id'];
        $duration = $this->config['duration'];
        $animationDuration = $this->config['animation_duration'];
        $autoDismiss = $this->config['auto_dismiss'] ? 'true' : 'false';
        $sound = $this->config['sound'] ? 'true' : 'false';
        $maxNotifications = $this->config['max_notifications'];

        return sprintf(
            '<script id="%s-script">%s</script>',
            $prefix,
            $this->getJavaScript($prefix, $containerId, $duration, $animationDuration, $autoDismiss, $sound, $maxNotifications)
        );
    }

    private function getJavaScript(string $prefix, string $containerId, int $duration, int $animationDuration, string $autoDismiss, string $sound, int $maxNotifications): string
    {
        return "
            (function() {
                'use strict';
                
                const config = {
                    prefix: '{$prefix}',
                    containerId: '{$containerId}',
                    duration: {$duration},
                    animationDuration: {$animationDuration},
                    autoDismiss: {$autoDismiss},
                    sound: {$sound},
                    maxNotifications: {$maxNotifications}
                };
                
                class NotifikasiManager {
                    constructor() {
                        this.container = null;
                        this.notifications = new Map();
                        this.init();
                    }
                    
                    init() {
                        this.container = document.getElementById(config.containerId);
                        if (!this.container) return;
                        
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
                        notifications.forEach((notification, index) => {
                            setTimeout(() => {
                                this.showNotification(notification);
                            }, index * 100);
                        });
                    }
                    
                    showNotification(notification) {
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
                        // Create audio context for system sounds
                        if (typeof AudioContext !== 'undefined' || typeof webkitAudioContext !== 'undefined') {
                            const AudioContextClass = AudioContext || webkitAudioContext;
                            const audioContext = new AudioContextClass();
                            const oscillator = audioContext.createOscillator();
                            const gainNode = audioContext.createGain();
                            
                            oscillator.connect(gainNode);
                            gainNode.connect(audioContext.destination);
                            
                            // Different frequencies for different levels
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
                        }
                    }
                }
                
                // Initialize when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        new NotifikasiManager();
                    });
                } else {
                    new NotifikasiManager();
                }
            })();
        ";
    }
}
