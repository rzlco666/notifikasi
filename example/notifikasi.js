/**
 * RzlcoNotifikasi - Apple-inspired notification system
 * JavaScript implementation for demo purposes
 */
class RzlcoNotifikasi {
    constructor(options = {}) {
        this.config = {
            position: options.position || 'top-right',
            theme: options.theme || 'auto',
            sound: options.sound !== false,
            duration: options.duration || 5000,
            maxNotifications: options.maxNotifications || 5,
            pauseOnHover: options.pauseOnHover !== false,
            rtl: options.rtl || false,
            animationDuration: options.animationDuration || 300,
            blurStrength: options.blurStrength || 20,
            borderRadius: options.borderRadius || 16,
            backdropOpacity: options.backdropOpacity || 0.8,
            zIndex: options.zIndex || 999999,
            containerClass: 'rzlco-notifikasi-container',
            notificationClass: 'rzlco-notifikasi',
            // New time display options
            showTime: options.showTime !== false,
            timeFormat: options.timeFormat || '12', // '12' or '24'
            // New close button options
            closeButtonStyle: options.closeButtonStyle || 'circle', // 'circle' or 'minimal'
            // New background options
            backgroundOpacity: options.backgroundOpacity || 0.85,
            backgroundBlur: options.backgroundBlur || 25
        };

        this.notifications = new Map();
        this.container = null;
        this.notificationCount = 0;
        
        this.init();
    }

    init() {
        this.createContainer();
        this.injectStyles();
        this.setupEventListeners();
    }

    createContainer() {
        // Remove existing container if any
        const existingContainer = document.getElementById('rzlco-notifikasi-container');
        if (existingContainer) {
            existingContainer.remove();
        }

        this.container = document.createElement('div');
        this.container.id = 'rzlco-notifikasi-container';
        this.container.className = `${this.config.containerClass} ${this.config.containerClass}-${this.config.position}`;
        this.container.style.cssText = `
            position: fixed;
            z-index: ${this.config.zIndex};
            pointer-events: none;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            direction: ${this.config.rtl ? 'rtl' : 'ltr'};
        `;

        this.setPosition(this.config.position);
        document.body.appendChild(this.container);
    }

    injectStyles() {
        const styleId = 'rzlco-notifikasi-styles';
        let styleElement = document.getElementById(styleId);
        
        if (!styleElement) {
            styleElement = document.createElement('style');
            styleElement.id = styleId;
            document.head.appendChild(styleElement);
        }

        styleElement.textContent = this.getStyles();
    }

    getStyles() {
        return `
            .${this.config.notificationClass} {
                position: relative;
                display: flex;
                align-items: flex-start;
                gap: 12px;
                min-width: 320px;
                max-width: 480px;
                padding: 16px 20px;
                margin-bottom: 12px;
                background: ${this.getThemeColor('background')};
                border: 1px solid ${this.getThemeColor('border')};
                border-radius: ${this.config.borderRadius}px;
                backdrop-filter: blur(${this.config.backgroundBlur}px);
                -webkit-backdrop-filter: blur(${this.config.backgroundBlur}px);
                box-shadow: 
                    0 8px 32px rgba(0, 0, 0, 0.12),
                    0 2px 8px rgba(0, 0, 0, 0.08),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
                color: ${this.getThemeColor('text')};
                pointer-events: auto;
                cursor: default;
                transition: all ${this.config.animationDuration}ms cubic-bezier(0.4, 0, 0.2, 1);
                transform: translateX(100%) scale(0.95);
                opacity: 0;
                overflow: hidden;
            }

            .${this.config.notificationClass}.show {
                transform: translateX(0) scale(1);
                opacity: 1;
            }

            .${this.config.notificationClass}.hide {
                transform: translateX(100%) scale(0.95);
                opacity: 0;
                margin-bottom: 0;
                max-height: 0;
                padding: 0;
            }

            .${this.config.notificationClass}:hover {
                transform: translateY(-2px) scale(1.02);
                box-shadow: 
                    0 12px 40px rgba(0, 0, 0, 0.15),
                    0 4px 12px rgba(0, 0, 0, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.15);
            }

            .${this.config.notificationClass}-icon {
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
                color: white;
            }

            .${this.config.notificationClass}-content {
                flex: 1;
                min-width: 0;
            }

            .${this.config.notificationClass}-title {
                margin: 0 0 4px 0;
                font-size: 14px;
                font-weight: 600;
                line-height: 1.2;
            }

            .${this.config.notificationClass}-message {
                margin: 0;
                font-size: 13px;
                opacity: 0.8;
                line-height: 1.3;
            }

            .${this.config.notificationClass}-time {
                font-size: 11px;
                opacity: 0.6;
                margin-right: 24px;
                font-weight: 500;
                font-variant-numeric: tabular-nums;
                position: absolute;
                top: 12px;
                right: 24px;
            }

            .${this.config.notificationClass}-close {
                position: absolute;
                top: 12px;
                right: 12px;
                background: none;
                border: none;
                color: inherit;
                font-size: 12px;
                cursor: pointer;
                opacity: 0.7;
                transition: all 0.2s ease;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: rgba(128, 128, 128, 0.2);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }

            .${this.config.notificationClass}-close:hover {
                opacity: 1;
                background: rgba(128, 128, 128, 0.3);
                transform: scale(1.1);
            }

            .${this.config.notificationClass}-close:active {
                transform: scale(0.95);
            }

            .${this.config.notificationClass}-success .${this.config.notificationClass}-icon {
                background: #22c55e;
            }

            .${this.config.notificationClass}-error .${this.config.notificationClass}-icon {
                background: #ef4444;
            }

            .${this.config.notificationClass}-warning .${this.config.notificationClass}-icon {
                background: #f59e0b;
            }

            .${this.config.notificationClass}-info .${this.config.notificationClass}-icon {
                background: #3b82f6;
            }

            .${this.config.containerClass}-left .${this.config.notificationClass} {
                transform: translateX(-100%) scale(0.95);
            }

            .${this.config.containerClass}-left .${this.config.notificationClass}.show {
                transform: translateX(0) scale(1);
            }

            .${this.config.containerClass}-left .${this.config.notificationClass}.hide {
                transform: translateX(-100%) scale(0.95);
            }

            .${this.config.containerClass}-center .${this.config.notificationClass} {
                transform: translateY(-100%) scale(0.95);
            }

            .${this.config.containerClass}-center .${this.config.notificationClass}.show {
                transform: translateY(0) scale(1);
            }

            .${this.config.containerClass}-center .${this.config.notificationClass}.hide {
                transform: translateY(-100%) scale(0.95);
            }

            @media (max-width: 640px) {
                .${this.config.containerClass} {
                    left: 10px !important;
                    right: 10px !important;
                    transform: none !important;
                }
                
                .${this.config.notificationClass} {
                    min-width: auto;
                    max-width: none;
                    margin-bottom: 8px;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .${this.config.notificationClass} {
                    transition: none;
                }
            }
        `;
    }

    getThemeColor(type) {
        const colors = {
            light: {
                background: `rgba(255, 255, 255, ${this.config.backgroundOpacity})`,
                border: 'rgba(0, 0, 0, 0.1)',
                text: 'rgba(0, 0, 0, 0.9)'
            },
            dark: {
                background: `rgba(30, 30, 30, ${this.config.backgroundOpacity})`,
                border: 'rgba(255, 255, 255, 0.1)',
                text: 'rgba(255, 255, 255, 0.9)'
            }
        };

        let theme = this.config.theme;
        if (theme === 'auto') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        return colors[theme][type] || colors.light[type];
    }

    setupEventListeners() {
        // Listen for theme changes
        if (this.config.theme === 'auto') {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                this.injectStyles();
            });
        }
    }

    getCurrentTime() {
        if (!this.config.showTime) return '';
        
        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();
        
        if (this.config.timeFormat === '24') {
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        } else {
            const period = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${minutes.toString().padStart(2, '0')} ${period}`;
        }
    }

    success(title, message = '', options = {}) {
        return this.show('success', title, message, options);
    }

    error(title, message = '', options = {}) {
        return this.show('error', title, message, options);
    }

    warning(title, message = '', options = {}) {
        return this.show('warning', title, message, options);
    }

    info(title, message = '', options = {}) {
        return this.show('info', title, message, options);
    }

    show(type, title, message = '', options = {}) {
        const notification = this.createNotification(type, title, message, options);
        this.addNotification(notification);
        this.limitNotifications();
        return this;
    }

    createNotification(type, title, message, options) {
        const id = `rzlco-notifikasi-${++this.notificationCount}`;
        const notification = document.createElement('div');
        notification.id = id;
        notification.className = `${this.config.notificationClass} ${this.config.notificationClass}-${type}`;
        notification.dataset.type = type;
        notification.dataset.id = id;

        const icon = this.getIcon(type);
        const time = this.getCurrentTime();
        const timeDisplay = time ? `<div class="${this.config.notificationClass}-time">${time}</div>` : '';
        const closeButton = '<button class="' + this.config.notificationClass + '-close" type="button">×</button>';

        notification.innerHTML = `
            <div class="${this.config.notificationClass}-icon">${icon}</div>
            <div class="${this.config.notificationClass}-content">
                <div class="${this.config.notificationClass}-title">${this.escapeHtml(title)}</div>
                ${message ? `<div class="${this.config.notificationClass}-message">${this.escapeHtml(message)}</div>` : ''}
            </div>
            ${timeDisplay}
            ${closeButton}
        `;

        // Add event listeners
        const closeBtn = notification.querySelector(`.${this.config.notificationClass}-close`);
        closeBtn.addEventListener('click', () => this.remove(id));

        if (this.config.pauseOnHover) {
            notification.addEventListener('mouseenter', () => this.pauseTimer(id));
            notification.addEventListener('mouseleave', () => this.resumeTimer(id));
        }

        return notification;
    }

    addNotification(notification) {
        this.container.appendChild(notification);
        
        // Trigger animation with delay for staggered effect
        setTimeout(() => {
            requestAnimationFrame(() => {
                notification.classList.add('show');
            });
        }, 50);

        // Play sound
        if (this.config.sound) {
            this.playSound(notification.dataset.type);
        }

        // Set auto-dismiss timer
        if (this.config.duration > 0) {
            this.setTimer(notification.id);
        }

        this.notifications.set(notification.id, {
            element: notification,
            timer: null,
            paused: false
        });
    }

    setTimer(id) {
        const notificationData = this.notifications.get(id);
        if (notificationData) {
            notificationData.timer = setTimeout(() => {
                this.remove(id);
            }, this.config.duration);
        }
    }

    pauseTimer(id) {
        const notificationData = this.notifications.get(id);
        if (notificationData && notificationData.timer) {
            clearTimeout(notificationData.timer);
            notificationData.paused = true;
        }
    }

    resumeTimer(id) {
        const notificationData = this.notifications.get(id);
        if (notificationData && notificationData.paused) {
            notificationData.paused = false;
            this.setTimer(id);
        }
    }

    remove(id) {
        const notificationData = this.notifications.get(id);
        if (!notificationData) return;

        const { element, timer } = notificationData;
        
        if (timer) {
            clearTimeout(timer);
        }

        element.classList.add('hide');
        
        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
            this.notifications.delete(id);
        }, this.config.animationDuration);
    }

    clear() {
        this.notifications.forEach((_, id) => {
            this.remove(id);
        });
    }

    clearAll() {
        this.clear();
    }

    limitNotifications() {
        const notificationElements = this.container.querySelectorAll(`.${this.config.notificationClass}`);
        if (notificationElements.length > this.config.maxNotifications) {
            const excess = notificationElements.length - this.config.maxNotifications;
            for (let i = 0; i < excess; i++) {
                const element = notificationElements[i];
                this.remove(element.id);
            }
        }
    }

    getIcon(type) {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }

    playSound(type) {
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

            oscillator.frequency.setValueAtTime(frequencies[type] || 700, audioContext.currentTime);
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (error) {
            console.warn('Could not play notification sound:', error);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Configuration methods
    setPosition(position) {
        this.config.position = position;
        if (this.container) {
            this.container.className = `${this.config.containerClass} ${this.config.containerClass}-${position}`;
            
            // Reset all position styles
            this.container.style.top = '';
            this.container.style.bottom = '';
            this.container.style.left = '';
            this.container.style.right = '';
            this.container.style.transform = '';

            // Apply new position
            const positions = {
                'top-left': { top: '20px', left: '20px' },
                'top-center': { top: '20px', left: '50%', transform: 'translateX(-50%)' },
                'top-right': { top: '20px', right: '20px' },
                'bottom-left': { bottom: '20px', left: '20px' },
                'bottom-center': { bottom: '20px', left: '50%', transform: 'translateX(-50%)' },
                'bottom-right': { bottom: '20px', right: '20px' }
            };

            const pos = positions[position] || positions['top-right'];
            Object.assign(this.container.style, pos);

            // Update container class for animations
            this.container.className = `${this.config.containerClass} ${this.config.containerClass}-${position.includes('left') ? 'left' : position.includes('center') ? 'center' : 'right'}`;
        }
        this.injectStyles();
    }

    setTheme(theme) {
        this.config.theme = theme;
        this.injectStyles();
    }

    setDuration(duration) {
        this.config.duration = duration;
    }

    setMaxNotifications(max) {
        this.config.maxNotifications = max;
        this.limitNotifications();
    }

    setSound(enabled) {
        this.config.sound = enabled;
    }

    setPauseOnHover(enabled) {
        this.config.pauseOnHover = enabled;
    }

    setRtl(enabled) {
        this.config.rtl = enabled;
        if (this.container) {
            this.container.style.direction = enabled ? 'rtl' : 'ltr';
        }
    }

    // New configuration methods
    setShowTime(show) {
        this.config.showTime = show;
        this.updateTimeDisplay();
    }

    setTimeFormat(format) {
        this.config.timeFormat = format;
        this.updateTimeDisplay();
    }

    updateTimeDisplay() {
        this.notifications.forEach((notificationData, id) => {
            const element = notificationData.element;
            const timeElement = element.querySelector(`.${this.config.notificationClass}-time`);
            const time = this.getCurrentTime();
            
            if (time && this.config.showTime) {
                if (timeElement) {
                    timeElement.textContent = time;
                } else {
                    // Create time element if it doesn't exist
                    const newTimeElement = document.createElement('div');
                    newTimeElement.className = `${this.config.notificationClass}-time`;
                    newTimeElement.textContent = time;
                    element.appendChild(newTimeElement);
                }
            } else if (timeElement) {
                // Remove time element if showTime is disabled
                timeElement.remove();
            }
        });
    }

    setBackgroundOpacity(opacity) {
        this.config.backgroundOpacity = opacity;
        this.injectStyles();
    }

    setBackgroundBlur(blur) {
        this.config.backgroundBlur = blur;
        this.injectStyles();
    }
}

// Make it globally available
window.RzlcoNotifikasi = RzlcoNotifikasi; 