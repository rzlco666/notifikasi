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
        $position = $this->config['position']->value;
        $zIndex = $this->config['z_index'];
        $rtl = $this->config['rtl'] ? 'rtl' : 'ltr';
        
        $notificationsHtml = $this->renderNotifications($notifications);

        return sprintf(
            '<div id="%s" class="%s-container %s-position-%s" style="z-index: %d; direction: %s;">%s</div>',
            $containerId,
            $this->config['css_prefix'],
            $this->config['css_prefix'],
            $position,
            $zIndex,
            $rtl,
            $notificationsHtml
        );
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
        return "
        <style id=\"rzlco-notifikasi-styles\">
            .rzlco-notifikasi-container {
                position: fixed !important;
                pointer-events: none;
                z-index: 999999999 !important;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                font-size: 14px;
                line-height: 1.4;
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
                pointer-events: auto !important;
                margin-bottom: 12px !important;
                min-width: 300px !important;
                max-width: 500px !important;
                border-radius: 16px !important;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
                background: rgba(255, 255, 255, 0.95) !important;
                border: 1px solid rgba(0, 0, 0, 0.1) !important;
                backdrop-filter: blur(25px) !important;
                -webkit-backdrop-filter: blur(25px) !important;
                opacity: 0 !important;
                transform: translateX(100%) scale(0.95) !important;
                transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
                overflow: hidden !important;
                position: relative !important;
                display: flex !important;
                align-items: flex-start !important;
                gap: 12px !important;
                padding: 16px 20px !important;
            }
            
            .rzlco-notifikasi-notification.rzlco-notifikasi-show {
                opacity: 1 !important;
                transform: translateX(0) scale(1) !important;
            }
            
            .rzlco-notifikasi-notification.rzlco-notifikasi-hide {
                opacity: 0 !important;
                transform: translateX(100%) scale(0.95) !important;
                margin-bottom: 0 !important;
                max-height: 0 !important;
                padding: 0 !important;
            }
            
            .rzlco-notifikasi-notification:hover {
                transform: translateY(-2px) scale(1.02) !important;
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            }
            
            .rzlco-notifikasi-content {
                flex: 1 !important;
                min-width: 0 !important;
            }
            
            .rzlco-notifikasi-icon {
                flex-shrink: 0 !important;
                width: 20px !important;
                height: 20px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin-top: 1px !important;
            }
            
            .rzlco-notifikasi-message {
                color: rgba(0, 0, 0, 0.9) !important;
                font-weight: 500 !important;
                word-break: break-word !important;
                margin: 0 !important;
            }
            
            .rzlco-notifikasi-time {
                font-size: 11px !important;
                opacity: 0.6 !important;
                margin-right: 24px !important;
                font-weight: 500 !important;
                font-variant-numeric: tabular-nums !important;
                color: rgba(0, 0, 0, 0.9) !important;
                position: absolute !important;
                top: 12px !important;
                right: 24px !important;
            }
            
            .rzlco-notifikasi-close {
                position: absolute !important;
                top: 12px !important;
                right: 12px !important;
                background: rgba(128, 128, 128, 0.2) !important;
                border: none !important;
                cursor: pointer !important;
                padding: 4px !important;
                border-radius: 50% !important;
                color: rgba(0, 0, 0, 0.9) !important;
                opacity: 0.7 !important;
                transition: all 0.2s ease !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 20px !important;
                height: 20px !important;
                backdrop-filter: blur(10px) !important;
                -webkit-backdrop-filter: blur(10px) !important;
                font-size: 12px !important;
            }
            
            .rzlco-notifikasi-close:hover {
                opacity: 1 !important;
                background: rgba(128, 128, 128, 0.3) !important;
                transform: scale(1.1) !important;
            }
            
            .rzlco-notifikasi-close:active {
                transform: scale(0.95) !important;
            }
            
            .rzlco-notifikasi-level-success {
                background: rgba(34, 197, 94, 0.05) !important;
                border-color: rgba(34, 197, 94, 0.2) !important;
            }
            
            .rzlco-notifikasi-level-success .rzlco-notifikasi-icon {
                color: #16a34a !important;
            }
            
            .rzlco-notifikasi-level-error {
                background: rgba(239, 68, 68, 0.05) !important;
                border-color: rgba(239, 68, 68, 0.2) !important;
            }
            
            .rzlco-notifikasi-level-error .rzlco-notifikasi-icon {
                color: #dc2626 !important;
            }
            
            .rzlco-notifikasi-level-warning {
                background: rgba(245, 158, 11, 0.05) !important;
                border-color: rgba(245, 158, 11, 0.2) !important;
            }
            
            .rzlco-notifikasi-level-warning .rzlco-notifikasi-icon {
                color: #d97706 !important;
            }
            
            .rzlco-notifikasi-level-info {
                background: rgba(59, 130, 246, 0.05) !important;
                border-color: rgba(59, 130, 246, 0.2) !important;
            }
            
            .rzlco-notifikasi-level-info .rzlco-notifikasi-icon {
                color: #2563eb !important;
            }
            
            .rzlco-notifikasi-container-left .rzlco-notifikasi-notification {
                transform: translateX(-100%) scale(0.95) !important;
            }
            
            .rzlco-notifikasi-container-left .rzlco-notifikasi-notification.rzlco-notifikasi-show {
                transform: translateX(0) scale(1) !important;
            }
            
            .rzlco-notifikasi-container-left .rzlco-notifikasi-notification.rzlco-notifikasi-hide {
                transform: translateX(-100%) scale(0.95) !important;
            }
            
            .rzlco-notifikasi-container-center .rzlco-notifikasi-notification {
                transform: translateY(-100%) scale(0.95) !important;
            }
            
            .rzlco-notifikasi-container-center .rzlco-notifikasi-notification.rzlco-notifikasi-show {
                transform: translateY(0) scale(1) !important;
            }
            
            .rzlco-notifikasi-container-center .rzlco-notifikasi-notification.rzlco-notifikasi-hide {
                transform: translateY(-100%) scale(0.95) !important;
            }
            
            /* Dark mode support */
            @media (prefers-color-scheme: dark) {
                .rzlco-notifikasi-notification {
                    background: rgba(30, 30, 30, 0.95) !important;
                    border-color: rgba(255, 255, 255, 0.1) !important;
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
            
            /* Force visibility for debugging */
            .rzlco-notifikasi-container {
                display: block !important;
                visibility: visible !important;
            }
            
            .rzlco-notifikasi-notification {
                display: flex !important;
                visibility: visible !important;
            }
            
            /* Override any template CSS that might hide notifications */
            .rzlco-notifikasi-container * {
                box-sizing: border-box !important;
            }
            
            /* Ensure notifications are always on top */
            .rzlco-notifikasi-container {
                position: fixed !important;
                z-index: 999999999 !important;
                pointer-events: none !important;
            }
            
            .rzlco-notifikasi-notification {
                pointer-events: auto !important;
                z-index: 999999999 !important;
            }
        </style>";
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
                
                // Global fallback for debugging
                window.debugNotifikasi = function() {
                    const container = document.getElementById(config.containerId);
                    if (container) {
                        console.log('Container found:', container);
                        const notifications = container.querySelectorAll('.' + config.prefix + '-notification');
                        console.log('Notifications found:', notifications.length);
                        notifications.forEach((n, i) => {
                            console.log('Notification ' + i + ':', n.id, n.className);
                        });
                    } else {
                        console.log('Container not found');
                    }
                };
            })();
        ";
    }
}
 