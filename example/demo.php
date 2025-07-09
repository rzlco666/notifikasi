<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Rzlco\Notifikasi\Notifikasi;
use Rzlco\Notifikasi\Storage\ArrayStorage;
use Rzlco\Notifikasi\Enums\NotificationPosition;

// Initialize notifikasi with custom config
$notifikasi = new Notifikasi(new ArrayStorage(), [
    'position' => NotificationPosition::TOP_RIGHT,
    'theme' => 'auto',
    'duration' => 5000,
    'sound' => true,
    'show_close_button' => true,
    'pause_on_hover' => true,
    'show_time' => true,
    'time_format' => '12',
    'background_opacity' => 0.85,
    'background_blur' => 25,
    'border_radius' => 16,
    'min_width' => 320,
    'max_width' => 480,
    'max_notifications' => 5,
]);

// Handle form submissions
if ($_POST) {
    $type = $_POST['type'] ?? 'info';
    $title = $_POST['title'] ?? 'Notification Title';
    $message = $_POST['message'] ?? '';
    
    // Override config with form values if provided
    $options = [];
    if (isset($_POST['position'])) $options['position'] = $_POST['position'];
    if (isset($_POST['theme'])) $options['theme'] = $_POST['theme'];
    if (isset($_POST['duration'])) $options['duration'] = (int)$_POST['duration'];
    if (isset($_POST['sound'])) $options['sound'] = $_POST['sound'] === '1';
    if (isset($_POST['show_time'])) $options['show_time'] = $_POST['show_time'] === '1';
    if (isset($_POST['time_format'])) $options['time_format'] = $_POST['time_format'];
    
    switch ($type) {
        case 'success':
            $notifikasi->success($title, $message, $options);
            break;
        case 'error':
            $notifikasi->error($title, $message, $options);
            break;
        case 'warning':
            $notifikasi->warning($title, $message, $options);
            break;
        case 'info':
        default:
            $notifikasi->info($title, $message, $options);
            break;
    }
}

// Add some demo notifications
if (empty($_POST)) {
    $notifikasi->success('Welcome! 🎉', 'Notifikasi library berhasil dimuat dengan design Apple yang cantik.');
    $notifikasi->info('Demo Ready ℹ️', 'Coba klik tombol di bawah untuk melihat berbagai jenis notifikasi.');
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi PHP Demo - Apple Design</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔔</text></svg>">
    <style>
        :root {
            --apple-black: #000000;
            --apple-white: #FFFFFF;
            --apple-gray-50: #F5F5F7;
            --apple-gray-100: #E5E5E7;
            --apple-gray-800: #3A3A3C;
            --apple-gray-900: #2C2C2E;
            --apple-gray-950: #1C1C1E;
            --apple-blue: #007AFF;
            --apple-green: #34C759;
            --apple-red: #FF3B30;
            --apple-orange: #FF9500;
            --font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'SF Pro Text', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background: var(--apple-black);
            color: var(--apple-white);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 32px;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 40px;
        }

        .card {
            background: var(--apple-gray-900);
            border: 1px solid var(--apple-gray-800);
            border-radius: 20px;
            padding: 32px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: var(--apple-gray-700);
        }

        .card h3 {
            font-size: 24px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--apple-gray-700);
            border-radius: 12px;
            background: var(--apple-gray-800);
            color: var(--apple-white);
            font-family: var(--font-family);
            font-size: 16px;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .button-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--apple-white);
            font-family: var(--font-family);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-success { background: var(--apple-green); }
        .btn-error { background: var(--apple-red); }
        .btn-warning { background: var(--apple-orange); }
        .btn-info { background: var(--apple-blue); }
        .btn-clear { 
            background: var(--apple-gray-600); 
            grid-column: 1 / -1;
        }

        .quick-demo {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--apple-blue);
        }

        .range-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .range-group input[type="range"] {
            flex: 1;
        }

        .range-value {
            min-width: 60px;
            text-align: center;
            font-variant-numeric: tabular-nums;
            color: var(--apple-blue);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .demo-grid {
                grid-template-columns: 1fr;
            }
            
            .button-group,
            .quick-demo {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Notifikasi PHP Demo</h1>
            <p class="subtitle">Apple-inspired liquid glass notifications dengan PHP backend</p>
        </div>

        <div class="demo-grid">
            <div class="card">
                <h3>🚀 Quick Demo</h3>
                <p style="margin-bottom: 24px; color: rgba(255, 255, 255, 0.7);">
                    Klik tombol untuk melihat notifikasi dengan pengaturan default.
                </p>
                
                <div class="quick-demo">
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="type" value="success">
                        <input type="hidden" name="title" value="Success! 🎉">
                        <input type="hidden" name="message" value="Operasi berhasil dilakukan dengan sempurna!">
                        <button type="submit" class="btn btn-success">Success</button>
                    </form>
                    
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="type" value="error">
                        <input type="hidden" name="title" value="Error! ❌">
                        <input type="hidden" name="message" value="Terjadi kesalahan yang tidak terduga.">
                        <button type="submit" class="btn btn-error">Error</button>
                    </form>
                    
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="type" value="warning">
                        <input type="hidden" name="title" value="Warning! ⚠️">
                        <input type="hidden" name="message" value="Harap periksa data sebelum melanjutkan.">
                        <button type="submit" class="btn btn-warning">Warning</button>
                    </form>
                    
                    <form method="post" style="display: contents;">
                        <input type="hidden" name="type" value="info">
                        <input type="hidden" name="title" value="Info! ℹ️">
                        <input type="hidden" name="message" value="Informasi penting yang perlu diketahui.">
                        <button type="submit" class="btn btn-info">Info</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <h3>⚙️ Custom Notification</h3>
                <form method="post">
                    <div class="form-group">
                        <label>Notification Type</label>
                        <select name="type">
                            <option value="success">Success</option>
                            <option value="error">Error</option>
                            <option value="warning">Warning</option>
                            <option value="info">Info</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="Custom Title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Message (Optional)</label>
                        <textarea name="message" placeholder="Detail message here..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Position</label>
                        <select name="position">
                            <option value="top-right">Top Right</option>
                            <option value="top-left">Top Left</option>
                            <option value="top-center">Top Center</option>
                            <option value="bottom-right">Bottom Right</option>
                            <option value="bottom-left">Bottom Left</option>
                            <option value="bottom-center">Bottom Center</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Theme</label>
                        <select name="theme">
                            <option value="auto">Auto</option>
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Duration (ms)</label>
                        <input type="number" name="duration" value="5000" min="1000" max="30000" step="500">
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="sound" name="sound" value="1" checked>
                            <label for="sound">Enable Sound</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="show_time" name="show_time" value="1" checked>
                            <label for="show_time">Show Time</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Time Format</label>
                        <select name="time_format">
                            <option value="12">12-hour (AM/PM)</option>
                            <option value="24">24-hour</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-info" style="width: 100%;">Show Custom Notification</button>
                </form>
            </div>
        </div>

        <div class="card">
            <h3>📖 Usage Example</h3>
            <pre style="background: var(--apple-gray-950); padding: 20px; border-radius: 12px; overflow-x: auto; color: var(--apple-gray-100); font-size: 14px; line-height: 1.5;"><code><?php echo htmlspecialchars('<?php
// Dasar
use Rzlco\Notifikasi\Facades\Notifikasi;

Notifikasi::success("Berhasil!", "Data berhasil disimpan ke database.");
Notifikasi::error("Error!", "Koneksi database gagal.");
Notifikasi::warning("Peringatan!", "Disk space hampir penuh.");
Notifikasi::info("Info", "Update tersedia untuk sistem.");

// Dengan opsi custom
Notifikasi::success("Success!", "Operation completed", [
    "position" => "top-center",
    "theme" => "light",
    "duration" => 10000,
    "sound" => false
]);

// Di Blade template
{!! app("notifikasi")->render() !!}'); ?></code></pre>
        </div>
    </div>

    <!-- Render notifications -->
    <?= $notifikasi->render() ?>
</body>
</html> 