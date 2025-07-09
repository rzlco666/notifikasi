# 🛠️ Advanced Usage Guide

## 📋 Table of Contents

- [🎯 **Core Architecture**](#-core-architecture) - Deep dive into system design
- [🔧 **Advanced Configuration**](#-advanced-configuration) - Expert-level customization
- [🎨 **Custom Theming**](#-custom-theming) - Creating beautiful themes
- [🚀 **Performance Optimization**](#-performance-optimization) - Speed and efficiency
- [🔊 **Audio System**](#-audio-system) - Sound design and implementation
- [📱 **Responsive Design**](#-responsive-design) - Mobile-first approach
- [♿ **Accessibility**](#-accessibility) - Inclusive design practices
- [🏗️ **Custom Storage Drivers**](#️-custom-storage-drivers) - Building your own storage
- [🎭 **Animation System**](#-animation-system) - Custom animations
- [🔒 **Security Considerations**](#-security-considerations) - Secure implementation

---

## 🎯 Core Architecture

### 🏛️ **Design Patterns**

The Notifikasi library follows several established design patterns:

**1. Strategy Pattern (Storage)**
```php
interface StorageInterface
{
    public function add(Notification $notification): void;
    public function get(): array;
    public function clear(): void;
}

// Session Strategy
class SessionStorage implements StorageInterface { ... }

// Array Strategy
class ArrayStorage implements StorageInterface { ... }

// Database Strategy (coming soon)
class DatabaseStorage implements StorageInterface { ... }
```

**2. Builder Pattern (Configuration)**
```php
$notifikasi = new Notifikasi(new SessionStorage())
    ->setTheme('dark')
    ->setPosition('bottom-right')
    ->setDuration(3000)
    ->setSound(false);
```

**3. Factory Pattern (Notification Creation)**
```php
class NotificationFactory
{
    public static function create(
        NotificationLevel $level,
        string $title,
        string $message,
        array $options = []
    ): Notification {
        return new Notification($level, $title, $message, $options);
    }
}
```

### 🔄 **Lifecycle Management**

```php
// 1. Creation
$notification = new Notification(
    NotificationLevel::SUCCESS,
    'Data Saved',
    'Your changes have been saved successfully.'
);

// 2. Storage
$storage->add($notification);

// 3. Rendering
$html = $renderer->renderNotification($notification);

// 4. Client-side Display
// JavaScript takes over for animations and interactions

// 5. Cleanup
$storage->clear();
```

---

## 🔧 Advanced Configuration

### 🎛️ **Complete Configuration Example**

```php
$config = [
    // Core Settings
    'position' => NotificationPosition::TOP_RIGHT,
    'duration' => 5000,
    'animation_duration' => 300,
    'max_notifications' => 10,
    'z_index' => 999999999,
    
    // Visual Design
    'theme' => 'auto',
    'background_opacity' => 0.85,
    'background_blur' => 25,
    'border_radius' => 16,
    'min_width' => 320,
    'max_width' => 480,
    
    // Interaction
    'auto_dismiss' => true,
    'closable' => true,
    'pause_on_hover' => true,
    'sound' => true,
    'show_time' => true,
    'time_format' => '12',
    
    // Layout
    'rtl' => false,
    'container_id' => 'rzlco-notifikasi-container',
    'css_prefix' => 'rzlco-notifikasi',
    
    // Performance
    'debounce_delay' => 100,
    'animation_fps' => 60,
    'lazy_load' => true,
    'preload_assets' => false,
    
    // Accessibility
    'screen_reader' => true,
    'keyboard_navigation' => true,
    'high_contrast' => false,
    'reduce_motion' => 'auto',
    
    // Custom Colors
    'colors' => [
        'success' => ['light' => '#22c55e', 'dark' => '#34c759'],
        'error' => ['light' => '#ef4444', 'dark' => '#ff453a'],
        'warning' => ['light' => '#f59e0b', 'dark' => '#ff9f0a'],
        'info' => ['light' => '#3b82f6', 'dark' => '#0a84ff'],
    ],
    
    // Sound Configuration
    'sound_config' => [
        'volume' => 0.3,
        'frequencies' => [
            'success' => [800, 1000],
            'error' => [400, 300],
            'warning' => [600, 800],
            'info' => [500, 700],
        ],
        'duration' => 100,
        'type' => 'sine',
    ],
];

$notifikasi = new Notifikasi(new SessionStorage(), $config);
```

### 🌐 **Environment-based Configuration**

```php
// Production Configuration
$productionConfig = [
    'debug' => false,
    'sound' => false,
    'animation_duration' => 200,
    'max_notifications' => 3,
    'preload_assets' => true,
    'performance' => [
        'debounce_delay' => 50,
        'animation_fps' => 30,
        'lazy_load' => true,
    ],
];

// Development Configuration
$developmentConfig = [
    'debug' => true,
    'sound' => true,
    'animation_duration' => 500,
    'max_notifications' => 10,
    'console_output' => true,
    'show_performance_metrics' => true,
];

// Choose based on environment
$config = app()->environment('production') 
    ? $productionConfig 
    : $developmentConfig;
```

---

## 🎨 Custom Theming

### 🌈 **Creating Custom Themes**

```php
class CustomThemeProvider
{
    public static function getDarkTheme(): array
    {
        return [
            'theme' => 'dark',
            'background_opacity' => 0.9,
            'background_blur' => 30,
            'colors' => [
                'success' => ['dark' => '#00FF41'],
                'error' => ['dark' => '#FF073A'],
                'warning' => ['dark' => '#FFD60A'],
                'info' => ['dark' => '#007AFF'],
                'background' => ['dark' => 'rgba(10, 10, 10, 0.9)'],
                'text' => ['dark' => '#FFFFFF'],
                'border' => ['dark' => 'rgba(255, 255, 255, 0.2)'],
            ],
        ];
    }
    
    public static function getGlassmorphismTheme(): array
    {
        return [
            'theme' => 'glassmorphism',
            'background_opacity' => 0.7,
            'background_blur' => 40,
            'border_radius' => 20,
            'colors' => [
                'background' => ['light' => 'rgba(255, 255, 255, 0.1)'],
                'border' => ['light' => 'rgba(255, 255, 255, 0.3)'],
            ],
            'custom_css' => '
                .rzlco-notifikasi-notification {
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    box-shadow: 
                        0 8px 32px rgba(31, 38, 135, 0.37),
                        inset 0 1px 0 rgba(255, 255, 255, 0.5);
                }
            ',
        ];
    }
}

// Usage
$notifikasi = new Notifikasi(
    new SessionStorage(),
    CustomThemeProvider::getDarkTheme()
);
```

### 🎭 **Dynamic Theme Switching**

```php
class DynamicTheming
{
    private Notifikasi $notifikasi;
    
    public function __construct(Notifikasi $notifikasi)
    {
        $this->notifikasi = $notifikasi;
    }
    
    public function switchTheme(string $theme): void
    {
        $config = match($theme) {
            'light' => $this->getLightConfig(),
            'dark' => $this->getDarkConfig(),
            'auto' => $this->getAutoConfig(),
            'custom' => $this->getCustomConfig(),
            default => $this->getAutoConfig(),
        };
        
        $this->notifikasi->updateConfig($config);
    }
    
    private function getTimeBasedTheme(): string
    {
        $hour = (int) date('H');
        
        return match(true) {
            $hour >= 6 && $hour < 18 => 'light',
            default => 'dark',
        };
    }
}
```

---

## 🚀 Performance Optimization

### ⚡ **Memory Management**

```php
class PerformantNotifikasi extends Notifikasi
{
    private int $memoryLimit = 50 * 1024 * 1024; // 50MB
    private array $performanceMetrics = [];
    
    public function add(
        NotificationLevel $level,
        string $title,
        string $message = '',
        array $options = []
    ): self {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Check memory usage
        if ($startMemory > $this->memoryLimit) {
            $this->cleanup();
        }
        
        $result = parent::add($level, $title, $message, $options);
        
        // Track performance
        $this->performanceMetrics[] = [
            'time' => microtime(true) - $startTime,
            'memory' => memory_get_usage(true) - $startMemory,
            'count' => $this->count(),
        ];
        
        return $result;
    }
    
    private function cleanup(): void
    {
        $this->clear();
        gc_collect_cycles();
    }
    
    public function getPerformanceMetrics(): array
    {
        return $this->performanceMetrics;
    }
}
```

### 🔧 **Lazy Loading Implementation**

```php
class LazyRenderer
{
    private bool $assetsLoaded = false;
    private array $queuedNotifications = [];
    
    public function render(): string
    {
        if (!$this->assetsLoaded) {
            $this->loadAssets();
            $this->assetsLoaded = true;
        }
        
        return $this->processQueue();
    }
    
    private function loadAssets(): void
    {
        // Load CSS and JS only when needed
        $this->preloadCriticalAssets();
    }
    
    private function preloadCriticalAssets(): void
    {
        echo '<link rel="preload" href="/assets/notifikasi.css" as="style">';
        echo '<link rel="preload" href="/assets/notifikasi.js" as="script">';
    }
}
```

---

## 🔊 Audio System

### 🎵 **Advanced Sound Configuration**

```php
class AdvancedAudioSystem
{
    private array $soundProfiles = [
        'subtle' => [
            'volume' => 0.1,
            'frequencies' => [
                'success' => [800],
                'error' => [400],
                'warning' => [600],
                'info' => [500],
            ],
            'duration' => 50,
            'type' => 'sine',
        ],
        'prominent' => [
            'volume' => 0.3,
            'frequencies' => [
                'success' => [800, 1000, 1200],
                'error' => [400, 300, 250],
                'warning' => [600, 800, 700],
                'info' => [500, 700, 600],
            ],
            'duration' => 150,
            'type' => 'triangle',
        ],
        'harmonic' => [
            'volume' => 0.2,
            'frequencies' => [
                'success' => [440, 554.37, 659.25], // A major chord
                'error' => [440, 523.25, 622.25],   // A diminished
                'warning' => [440, 554.37, 698.46], // A suspended
                'info' => [440, 523.25, 659.25],    // A minor
            ],
            'duration' => 200,
            'type' => 'sine',
        ],
    ];
    
    public function generateSoundScript(string $profile = 'subtle'): string
    {
        $config = $this->soundProfiles[$profile] ?? $this->soundProfiles['subtle'];
        
        return sprintf(
            'window.NotifikasiAudio = new AudioSystem(%s);',
            json_encode($config)
        );
    }
}
```

### 🎼 **Custom Audio Implementation**

```javascript
class AdvancedAudioSystem {
    constructor(config) {
        this.config = config;
        this.context = null;
        this.gainNode = null;
        this.init();
    }
    
    init() {
        try {
            this.context = new (window.AudioContext || window.webkitAudioContext)();
            this.gainNode = this.context.createGain();
            this.gainNode.connect(this.context.destination);
            this.gainNode.gain.value = this.config.volume;
        } catch (error) {
            console.warn('Audio not supported:', error);
        }
    }
    
    playChord(frequencies, duration = 100) {
        if (!this.context) return;
        
        frequencies.forEach((freq, index) => {
            const oscillator = this.context.createOscillator();
            const envelope = this.context.createGain();
            
            oscillator.connect(envelope);
            envelope.connect(this.gainNode);
            
            oscillator.frequency.value = freq;
            oscillator.type = this.config.type;
            
            // ADSR envelope
            const now = this.context.currentTime;
            envelope.gain.setValueAtTime(0, now);
            envelope.gain.linearRampToValueAtTime(0.3, now + 0.01); // Attack
            envelope.gain.exponentialRampToValueAtTime(0.1, now + 0.05); // Decay
            envelope.gain.setValueAtTime(0.1, now + duration/1000 - 0.05); // Sustain
            envelope.gain.exponentialRampToValueAtTime(0.01, now + duration/1000); // Release
            
            oscillator.start(now);
            oscillator.stop(now + duration/1000);
        });
    }
}
```

---

## 📱 Responsive Design

### 📐 **Advanced Breakpoint System**

```php
class ResponsiveConfiguration
{
    private array $breakpoints = [
        'xs' => 320,   // Small phones
        'sm' => 640,   // Large phones
        'md' => 768,   // Tablets
        'lg' => 1024,  // Small laptops
        'xl' => 1280,  // Desktops
        '2xl' => 1536, // Large screens
    ];
    
    public function generateResponsiveCSS(): string
    {
        $css = '';
        
        foreach ($this->breakpoints as $size => $width) {
            $css .= $this->generateBreakpointCSS($size, $width);
        }
        
        return $css;
    }
    
    private function generateBreakpointCSS(string $size, int $width): string
    {
        return match($size) {
            'xs' => "
                @media (max-width: {$width}px) {
                    .rzlco-notifikasi-container {
                        left: 5px !important;
                        right: 5px !important;
                    }
                    .rzlco-notifikasi-notification {
                        min-width: auto !important;
                        font-size: 12px !important;
                        padding: 12px 16px !important;
                    }
                }
            ",
            'sm' => "
                @media (max-width: {$width}px) {
                    .rzlco-notifikasi-container {
                        left: 10px !important;
                        right: 10px !important;
                    }
                    .rzlco-notifikasi-notification {
                        margin-bottom: 8px !important;
                    }
                }
            ",
            'md' => "
                @media (min-width: {$width}px) {
                    .rzlco-notifikasi-notification {
                        min-width: 350px !important;
                        max-width: 500px !important;
                    }
                }
            ",
            default => ''
        };
    }
}
```

### 🎯 **Device-Specific Optimizations**

```php
class DeviceOptimization
{
    public function getDeviceConfig(): array
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        return match(true) {
            $this->isMobile($userAgent) => $this->getMobileConfig(),
            $this->isTablet($userAgent) => $this->getTabletConfig(),
            $this->isDesktop($userAgent) => $this->getDesktopConfig(),
            default => $this->getDefaultConfig(),
        };
    }
    
    private function getMobileConfig(): array
    {
        return [
            'position' => 'top-center',
            'duration' => 3000,
            'animation_duration' => 200,
            'sound' => false,
            'max_notifications' => 3,
            'background_blur' => 15, // Reduced for performance
            'mobile' => [
                'full_width' => true,
                'margin' => 5,
                'touch_friendly' => true,
            ],
        ];
    }
    
    private function getDesktopConfig(): array
    {
        return [
            'position' => 'top-right',
            'duration' => 5000,
            'animation_duration' => 300,
            'sound' => true,
            'max_notifications' => 5,
            'background_blur' => 25,
            'hover_effects' => true,
            'keyboard_shortcuts' => true,
        ];
    }
}
```

---

## ♿ Accessibility

### 🎯 **ARIA Implementation**

```php
class AccessibilityRenderer
{
    public function renderNotificationWithAria(Notification $notification): string
    {
        $level = $notification->getLevel();
        $ariaLevel = $this->getAriaLevel($level);
        $ariaLive = $this->getAriaLive($level);
        
        return sprintf(
            '<div class="rzlco-notifikasi-notification" 
                  role="%s" 
                  aria-live="%s" 
                  aria-atomic="true"
                  aria-describedby="notification-content-%s"
                  tabindex="0">
                <div class="rzlco-notifikasi-icon" aria-hidden="true">%s</div>
                <div id="notification-content-%s" class="rzlco-notifikasi-content">
                    <div class="rzlco-notifikasi-title">%s</div>
                    <div class="rzlco-notifikasi-message">%s</div>
                </div>
                <button class="rzlco-notifikasi-close" 
                        aria-label="Close notification: %s"
                        type="button">×</button>
            </div>',
            $ariaLevel,
            $ariaLive,
            $notification->getId(),
            $this->getIcon($level),
            $notification->getId(),
            htmlspecialchars($notification->getTitle()),
            htmlspecialchars($notification->getMessage()),
            htmlspecialchars($notification->getTitle())
        );
    }
    
    private function getAriaLevel(NotificationLevel $level): string
    {
        return match($level) {
            NotificationLevel::ERROR => 'alert',
            NotificationLevel::WARNING => 'alert',
            default => 'status',
        };
    }
    
    private function getAriaLive(NotificationLevel $level): string
    {
        return match($level) {
            NotificationLevel::ERROR => 'assertive',
            default => 'polite',
        };
    }
}
```

### ⌨️ **Keyboard Navigation**

```javascript
class AccessibilityManager {
    constructor() {
        this.focusableNotifications = [];
        this.currentFocus = -1;
        this.init();
    }
    
    init() {
        this.setupKeyboardListeners();
        this.setupFocusManagement();
    }
    
    setupKeyboardListeners() {
        document.addEventListener('keydown', (e) => {
            switch(e.key) {
                case 'Escape':
                    this.closeFocusedNotification();
                    break;
                case 'Tab':
                    this.handleTabNavigation(e);
                    break;
                case 'ArrowUp':
                case 'ArrowDown':
                    this.handleArrowNavigation(e);
                    break;
                case 'Enter':
                case ' ':
                    this.activateFocusedElement(e);
                    break;
            }
        });
    }
    
    handleArrowNavigation(e) {
        e.preventDefault();
        
        if (e.key === 'ArrowDown') {
            this.currentFocus = Math.min(
                this.currentFocus + 1, 
                this.focusableNotifications.length - 1
            );
        } else {
            this.currentFocus = Math.max(this.currentFocus - 1, 0);
        }
        
        this.updateFocus();
    }
    
    updateFocus() {
        this.focusableNotifications.forEach((notification, index) => {
            if (index === this.currentFocus) {
                notification.focus();
                notification.setAttribute('aria-selected', 'true');
            } else {
                notification.setAttribute('aria-selected', 'false');
            }
        });
    }
}
```

---

## 🏗️ Custom Storage Drivers

### 💾 **Database Storage Implementation**

```php
class DatabaseStorage implements StorageInterface
{
    private PDO $connection;
    private string $table;
    private ?string $userId;
    
    public function __construct(PDO $connection, string $table = 'notifications', ?string $userId = null)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->userId = $userId;
    }
    
    public function add(Notification $notification): void
    {
        $sql = "INSERT INTO {$this->table} 
                (id, user_id, level, title, message, options, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            $notification->getId(),
            $this->userId,
            $notification->getLevel()->value,
            $notification->getTitle(),
            $notification->getMessage(),
            json_encode($notification->getOptions()),
            date('Y-m-d H:i:s', $notification->getTimestamp()),
        ]);
    }
    
    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? OR user_id IS NULL 
                ORDER BY created_at DESC 
                LIMIT 10";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$this->userId]);
        
        $notifications = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = $this->hydrate($row);
        }
        
        return $notifications;
    }
    
    public function clear(): void
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? OR user_id IS NULL";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$this->userId]);
    }
    
    private function hydrate(array $data): Notification
    {
        return Notification::fromArray([
            'id' => $data['id'],
            'level' => $data['level'],
            'title' => $data['title'],
            'message' => $data['message'],
            'options' => json_decode($data['options'], true),
            'timestamp' => strtotime($data['created_at']),
        ]);
    }
}
```

### 📡 **Redis Storage Implementation**

```php
class RedisStorage implements StorageInterface
{
    private Redis $redis;
    private string $keyPrefix;
    private int $ttl;
    
    public function __construct(Redis $redis, string $keyPrefix = 'notifications:', int $ttl = 3600)
    {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
        $this->ttl = $ttl;
    }
    
    public function add(Notification $notification): void
    {
        $key = $this->keyPrefix . session_id();
        $data = json_encode($notification->toArray());
        
        $this->redis->lPush($key, $data);
        $this->redis->expire($key, $this->ttl);
        
        // Limit to max 50 notifications
        $this->redis->lTrim($key, 0, 49);
    }
    
    public function get(): array
    {
        $key = $this->keyPrefix . session_id();
        $items = $this->redis->lRange($key, 0, -1);
        
        $notifications = [];
        foreach ($items as $item) {
            $data = json_decode($item, true);
            if ($data) {
                $notifications[] = Notification::fromArray($data);
            }
        }
        
        return $notifications;
    }
    
    public function clear(): void
    {
        $key = $this->keyPrefix . session_id();
        $this->redis->del($key);
    }
}
```

---

## 🎭 Animation System

### ✨ **Custom Animation Engine**

```php
class AnimationEngine
{
    private array $animations = [
        'slide' => [
            'enter' => 'translateX(100%)',
            'show' => 'translateX(0)',
            'exit' => 'translateX(100%)',
        ],
        'fade' => [
            'enter' => 'opacity: 0; transform: scale(0.9)',
            'show' => 'opacity: 1; transform: scale(1)',
            'exit' => 'opacity: 0; transform: scale(0.9)',
        ],
        'bounce' => [
            'enter' => 'transform: translateY(-100%) scale(0.8)',
            'show' => 'transform: translateY(0) scale(1)',
            'exit' => 'transform: translateY(-100%) scale(0.8)',
        ],
        'elastic' => [
            'enter' => 'transform: scale(0.3) rotate(-10deg)',
            'show' => 'transform: scale(1) rotate(0deg)',
            'exit' => 'transform: scale(0.3) rotate(10deg)',
        ],
    ];
    
    public function generateAnimationCSS(string $type = 'slide'): string
    {
        $animation = $this->animations[$type] ?? $this->animations['slide'];
        
        return "
            .rzlco-notifikasi-notification {
                {$animation['enter']};
                transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            }
            
            .rzlco-notifikasi-notification.rzlco-notifikasi-show {
                {$animation['show']};
            }
            
            .rzlco-notifikasi-notification.rzlco-notifikasi-hide {
                {$animation['exit']};
            }
        ";
    }
}
```

### 🎬 **Physics-based Animations**

```javascript
class PhysicsAnimator {
    constructor(element, config = {}) {
        this.element = element;
        this.config = {
            stiffness: 300,
            damping: 30,
            mass: 1,
            ...config
        };
        
        this.position = 0;
        this.velocity = 0;
        this.target = 0;
        this.animationId = null;
    }
    
    animate(to) {
        this.target = to;
        this.startAnimation();
    }
    
    startAnimation() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
        
        const step = () => {
            const force = -this.config.stiffness * (this.position - this.target);
            const damping = -this.config.damping * this.velocity;
            
            this.velocity += (force + damping) / this.config.mass;
            this.position += this.velocity;
            
            this.element.style.transform = `translateX(${this.position}px)`;
            
            if (Math.abs(this.velocity) > 0.1 || Math.abs(this.position - this.target) > 0.1) {
                this.animationId = requestAnimationFrame(step);
            } else {
                this.position = this.target;
                this.velocity = 0;
                this.element.style.transform = `translateX(${this.position}px)`;
            }
        };
        
        step();
    }
}
```

---

## 🔒 Security Considerations

### 🛡️ **XSS Prevention**

```php
class SecurityRenderer
{
    private array $allowedTags = ['b', 'i', 'strong', 'em', 'br'];
    
    public function sanitizeContent(string $content): string
    {
        // Strip all HTML except allowed tags
        $content = strip_tags($content, '<' . implode('><', $this->allowedTags) . '>');
        
        // Escape any remaining special characters
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        // Additional security: remove javascript: URLs
        $content = preg_replace('/javascript:/i', '', $content);
        
        return $content;
    }
    
    public function validateOptions(array $options): array
    {
        $whitelist = [
            'duration', 'position', 'theme', 'sound', 
            'closable', 'show_time', 'time_format', 'auto_dismiss'
        ];
        
        return array_intersect_key($options, array_flip($whitelist));
    }
}
```

### 🔐 **Content Security Policy**

```php
class CSPManager
{
    public function generateCSPHeader(): string
    {
        return "Content-Security-Policy: " . implode('; ', [
            "default-src 'self'",
            "style-src 'self' 'unsafe-inline'", // For dynamic styles
            "script-src 'self' 'unsafe-inline'", // For inline scripts
            "img-src 'self' data:",
            "font-src 'self'",
            "media-src 'none'", // No audio files, using Web Audio API
            "object-src 'none'",
            "frame-src 'none'",
        ]);
    }
}
```

---

## 🧪 Testing Advanced Features

### 🎯 **Performance Testing**

```php
class PerformanceTest extends TestCase
{
    public function testMemoryUsage(): void
    {
        $initialMemory = memory_get_usage(true);
        
        $notifikasi = new Notifikasi(new ArrayStorage());
        
        // Add 1000 notifications
        for ($i = 0; $i < 1000; $i++) {
            $notifikasi->success("Test $i", "Message $i");
        }
        
        $memoryAfterAdding = memory_get_usage(true);
        $notifikasi->render();
        $memoryAfterRender = memory_get_usage(true);
        
        // Memory should not increase significantly
        $memoryIncrease = $memoryAfterRender - $initialMemory;
        $this->assertLessThan(10 * 1024 * 1024, $memoryIncrease); // Less than 10MB
    }
    
    public function testRenderingSpeed(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        
        for ($i = 0; $i < 100; $i++) {
            $notifikasi->success("Test $i", "Message $i");
        }
        
        $startTime = microtime(true);
        $output = $notifikasi->render();
        $endTime = microtime(true);
        
        $renderTime = $endTime - $startTime;
        
        // Rendering should be fast
        $this->assertLessThan(0.1, $renderTime); // Less than 100ms
        $this->assertNotEmpty($output);
    }
}
```

### 🔒 **Security Testing**

```php
class SecurityTest extends TestCase
{
    public function testXSSPrevention(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        
        $maliciousScript = '<script>alert("XSS")</script>';
        $notifikasi->success('Test', $maliciousScript);
        
        $output = $notifikasi->render();
        
        // Should not contain unescaped script tags
        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('alert("XSS")', $output);
    }
    
    public function testHTMLEscaping(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        
        $htmlContent = '<b>Bold</b> & <i>Italic</i>';
        $notifikasi->success('Test', $htmlContent);
        
        $output = $notifikasi->render();
        
        // HTML should be properly escaped
        $this->assertStringContainsString('&lt;b&gt;Bold&lt;/b&gt;', $output);
        $this->assertStringContainsString('&amp;', $output);
    }
}
```

---

This advanced guide covers the sophisticated aspects of the Notifikasi library, providing expert-level insights and practical examples for building robust, secure, and performant notification systems. 