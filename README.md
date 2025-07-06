# Notifikasi

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rzlco666/notifikasi.svg?style=flat-square)](https://packagist.org/packages/rzlco666/notifikasi)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rzlco666/notifikasi/run-tests?label=tests)](https://github.com/rzlco666/notifikasi/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/rzlco666/notifikasi/Check%20&%20fix%20styling?label=code%20style)](https://github.com/rzlco666/notifikasi/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rzlco666/notifikasi.svg?style=flat-square)](https://packagist.org/packages/rzlco666/notifikasi)

A beautiful, Apple-inspired liquid glass notification system for PHP and Laravel applications. Built with modern web technologies and optimized for performance.

## Features

🎨 **Apple Design System** - Consistent with Apple Human Interface Guidelines  
✨ **Liquid Glass Effect** - Modern backdrop blur and transparency effects  
🔊 **Sound Effects** - Subtle audio feedback using Web Audio API  
📱 **Responsive Design** - Automatically adapts to mobile and desktop  
🚀 **High Performance** - Optimized animations and efficient DOM manipulation  
♿ **Accessibility** - Screen reader support and keyboard navigation  
🌍 **RTL Support** - Right-to-left language support  
🎯 **Multiple Positions** - 6 different positioning options  
🎭 **Theme Support** - Auto, light, and dark themes  
⚙️ **Highly Configurable** - Extensive customization options  
🕐 **Time Display** - Real-time clock display on notifications  
🎵 **Audio Feedback** - Different sound frequencies for each notification type  
📊 **Progress Tracking** - Built-in progress indicators  
🔧 **Storage Drivers** - Session and Array storage support  

## Requirements

- PHP 8.2 or higher
- Laravel 12.0 or higher (for Laravel integration)

## Installation

You can install the package via composer:

```bash
composer require rzlco666/notifikasi
```

### Laravel Integration

The package will automatically register its service provider and facade.

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Rzlco\NotifikasiServiceProvider" --tag="config"
```

Optionally, you can publish the views:

```bash
php artisan vendor:publish --provider="Rzlco\NotifikasiServiceProvider" --tag="views"
```

## Usage

### PHP Native

```php
use Rzlco\Notifikasi;
use Rzlco\Storage\SessionStorage;

// Initialize with session storage
$notifikasi = new Notifikasi(new SessionStorage());

// Add notifications
$notifikasi->success('Success!', 'Your data has been saved successfully.');
$notifikasi->error('Error!', 'Something went wrong.');
$notifikasi->warning('Warning!', 'Please check your input.');
$notifikasi->info('Info!', 'Here is some useful information.');

// Render notifications
echo $notifikasi->render();
```

### Laravel

#### Using Facade

```php
use Rzlco\Facades\Notifikasi;

// Add notifications
Notifikasi::success('Success!', 'Your data has been saved successfully.');
Notifikasi::error('Error!', 'Something went wrong.');
Notifikasi::warning('Warning!', 'Please check your input.');
Notifikasi::info('Info!', 'Here is some useful information.');
```

#### Using Dependency Injection

```php
use Rzlco\Contracts\NotifikasiInterface;

class UserController extends Controller
{
    public function store(Request $request, NotifikasiInterface $notifikasi)
    {
        // Your logic here
        
        $notifikasi->success('User Created!', 'The user has been created successfully.');
        
        return redirect()->back();
    }
}
```

#### In Blade Templates

```blade
{{-- Include the notification component --}}
<x-notifikasi />

{{-- Or use the directive --}}
@notifikasi

{{-- With custom options --}}
<x-notifikasi 
    position="top-left" 
    theme="dark" 
    :sound="false" 
    :duration="3000" 
/>
```

### Advanced Usage

#### Custom Options

```php
$notifikasi->success('Success!', [
    'duration' => 3000,
    'closable' => false,
    'theme' => 'dark',
    'position' => 'bottom-right',
    'sound' => true,
    'show_time' => true,
    'time_format' => '24',
    'background_opacity' => 0.9,
    'background_blur' => 30,
    'close_button_style' => 'circle'
]);
```

#### Configuration

```php
$notifikasi->setPosition('top-left')
          ->setTheme('dark')
          ->setDuration(3000)
          ->setSound(false)
          ->setMaxNotifications(10);
```

#### Custom Storage

```php
use Rzlco\Storage\ArrayStorage;

$notifikasi = new Notifikasi(new ArrayStorage());
```

## Configuration

### Laravel Configuration

The configuration file `config/notifikasi.php` allows you to customize:

```php
return [
    'default' => 'session',
    
    'storages' => [
        'session' => [
            'driver' => 'session',
            'key' => 'rzlco_notifications',
        ],
        'array' => [
            'driver' => 'array',
        ],
    ],
    
    'defaults' => [
        'position' => 'top-right',
        'duration' => 5000,
        'theme' => 'auto',
        'sound' => true,
        'closable' => true,
        'pause_on_hover' => true,
        'rtl' => false,
        'max_notifications' => 5,
        'animation_duration' => 300,
        'blur_strength' => 20,
        'border_radius' => 16,
        'backdrop_opacity' => 0.8,
        'show_time' => true,
        'time_format' => '12',
        'background_opacity' => 0.85,
        'background_blur' => 25,
        'close_button_style' => 'circle',
    ],
    
    // Apple Design System Colors
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
    
    // Sound Configuration
    'sound' => [
        'enabled' => true,
        'volume' => 0.3,
        'frequencies' => [
            'success' => [800, 1000],
            'error' => [400, 300],
            'warning' => [600, 800],
            'info' => [500, 700],
        ],
    ],
    
    // Performance Settings
    'performance' => [
        'debounce_delay' => 100,
        'animation_fps' => 60,
        'lazy_load' => true,
        'preload_assets' => false,
    ],
    
    // Accessibility Settings
    'accessibility' => [
        'reduce_motion' => 'auto',
        'high_contrast' => false,
        'screen_reader' => true,
        'focus_management' => true,
    ],
];
```

### Available Positions

- `top-left`
- `top-center`
- `top-right`
- `bottom-left`
- `bottom-center`
- `bottom-right`

### Available Themes

- `auto` - Automatically detects system preference
- `light` - Light theme
- `dark` - Dark theme

### Time Format Options

- `12` - 12-hour format (e.g., "3:45 PM")
- `24` - 24-hour format (e.g., "15:45")

## JavaScript API

The package includes a JavaScript API for dynamic notifications:

```javascript
// Initialize
const notifikasi = new RzlcoNotifikasi({
    position: 'top-right',
    theme: 'auto',
    sound: true,
    duration: 5000,
    showTime: true,
    timeFormat: '12'
});

// Show notifications
notifikasi.success('Success!', 'Your operation completed successfully.');
notifikasi.error('Error!', 'Something went wrong.');
notifikasi.warning('Warning!', 'Please be careful.');
notifikasi.info('Info!', 'Here is some useful information.');

// Clear notifications
notifikasi.clear();
notifikasi.clearAll();

// Update settings
notifikasi.setPosition('bottom-right');
notifikasi.setTheme('dark');
notifikasi.setDuration(3000);
```

## Styling

The package includes Apple-inspired CSS with liquid glass effects:

```css
/* Custom CSS variables */
:root {
    --rzlco-notifikasi-blur: 20px;
    --rzlco-notifikasi-radius: 16px;
    --rzlco-notifikasi-backdrop-opacity: 0.8;
    --rzlco-notifikasi-animation-duration: 300ms;
    --rzlco-notifikasi-animation-easing: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Customize colors */
.rzlco-notifikasi-success {
    --current-color: #34C759;
}

/* Dark theme support */
@media (prefers-color-scheme: dark) {
    :root {
        --rzlco-notifikasi-bg: rgba(28, 28, 30, 0.8);
        --rzlco-notifikasi-text: #F2F2F7;
    }
}
```

## Testing

Run the tests with:

```bash
composer test
```

Run static analysis:

```bash
composer phpstan
```

Run code style checks:

```bash
composer phpcs
```

Fix code style automatically:

```bash
composer phpcbf
```

## Performance

The package is optimized for performance:

- **Efficient DOM manipulation** - Minimal DOM updates
- **CSS transforms** - Hardware-accelerated animations
- **Debounced operations** - Prevents excessive updates
- **Lazy loading** - Assets loaded only when needed
- **Memory management** - Automatic cleanup of old notifications
- **RequestAnimationFrame** - Smooth animations
- **Web Audio API** - Efficient sound generation

## Browser Support

- Chrome 58+
- Firefox 53+
- Safari 10+
- Edge 79+

## Accessibility

The package includes comprehensive accessibility features:

- **Screen reader support** - ARIA labels and live regions
- **Keyboard navigation** - Full keyboard support
- **Reduced motion** - Respects user preferences
- **High contrast** - Compatible with high contrast mode
- **Focus management** - Proper focus handling
- **RTL support** - Right-to-left language support

## Security

The package handles user input safely:

- **XSS protection** - All content is properly escaped
- **Content Security Policy** - Compatible with strict CSP
- **No inline scripts** - All JavaScript is external
- **Session security** - Secure session handling

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Rzlco666](https://github.com/rzlco666)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Roadmap

### Version 1.x
- [x] Basic notification system
- [x] Apple liquid glass design
- [x] Laravel integration
- [x] Multiple storage drivers
- [x] Comprehensive testing
- [x] Time display feature
- [x] Audio feedback system
- [x] RTL support
- [x] Accessibility features
- [x] Performance optimizations

### Version 2.x (Planned)
- [ ] Database storage driver
- [ ] Queue integration
- [ ] Email notifications
- [ ] Push notifications
- [ ] Advanced theming system
- [ ] Plugin system
- [ ] React/Vue components
- [ ] TypeScript definitions
- [ ] Progress indicators
- [ ] Custom notification types

### Version 3.x (Future)
- [ ] Real-time notifications
- [ ] WebSocket support
- [ ] Advanced analytics
- [ ] A/B testing framework
- [ ] Multi-language support
- [ ] Advanced animations
- [ ] Enterprise features
- [ ] Mobile app integration

## Support

If you discover any issues or have questions, please:

1. Check the [documentation](https://github.com/rzlco666/notifikasi/wiki)
2. Search [existing issues](https://github.com/rzlco666/notifikasi/issues)
3. Create a [new issue](https://github.com/rzlco666/notifikasi/issues/new)

For commercial support, please contact [support@rzlco.com](mailto:support@rzlco.com).

## Acknowledgments

- Inspired by Apple's Human Interface Guidelines
- Built with modern web standards
- Optimized for performance and accessibility
- Designed for developer experience
- Liquid glass effect implementation
- Web Audio API integration

---

Made with ❤️ by [Rzlco666](https://github.com/rzlco666) 