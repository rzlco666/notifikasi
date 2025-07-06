<?php

namespace Rzlco\Notifikasi\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Rzlco\Notifikasi\Notifikasi;
use Rzlco\Notifikasi\Notification;
use Rzlco\Notifikasi\Storage\ArrayStorage;
use Rzlco\Notifikasi\Storage\SessionStorage;
use Rzlco\Notifikasi\Enums\NotificationLevel;
use Rzlco\Notifikasi\Enums\NotificationPosition;

class NotifikasiTest extends TestCase
{
    private Notifikasi $notifikasi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notifikasi = new Notifikasi(new ArrayStorage());
    }

    public function testCanCreateNotifikasiInstance(): void
    {
        $this->assertInstanceOf(Notifikasi::class, $this->notifikasi);
    }

    public function testCanAddSuccessNotification(): void
    {
        $result = $this->notifikasi->success('This is a success message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is a success message', $notification->getMessage());
        $this->assertEquals(NotificationLevel::SUCCESS, $notification->getLevel());
    }

    public function testCanAddErrorNotification(): void
    {
        $result = $this->notifikasi->error('This is an error message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is an error message', $notification->getMessage());
        $this->assertEquals(NotificationLevel::ERROR, $notification->getLevel());
    }

    public function testCanAddWarningNotification(): void
    {
        $result = $this->notifikasi->warning('This is a warning message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is a warning message', $notification->getMessage());
        $this->assertEquals(NotificationLevel::WARNING, $notification->getLevel());
    }

    public function testCanAddInfoNotification(): void
    {
        $result = $this->notifikasi->info('This is an info message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is an info message', $notification->getMessage());
        $this->assertEquals(NotificationLevel::INFO, $notification->getLevel());
    }

    public function testCanAddMultipleNotifications(): void
    {
        $this->notifikasi
            ->success('First success')
            ->error('First error')
            ->warning('First warning')
            ->info('First info');

        $this->assertCount(4, $this->notifikasi->getNotifications());
    }

    public function testCanClearNotifications(): void
    {
        $this->notifikasi
            ->success('First success')
            ->error('First error');

        $this->assertCount(2, $this->notifikasi->getNotifications());

        $this->notifikasi->clear();
        $this->assertCount(0, $this->notifikasi->getNotifications());
    }

    public function testHasNotifications(): void
    {
        $this->assertFalse($this->notifikasi->hasNotifications());

        $this->notifikasi->success('Test message');
        $this->assertTrue($this->notifikasi->hasNotifications());

        $this->notifikasi->clear();
        $this->assertFalse($this->notifikasi->hasNotifications());
    }

    public function testCountNotifications(): void
    {
        $this->assertEquals(0, $this->notifikasi->count());

        $this->notifikasi->success('Test 1');
        $this->assertEquals(1, $this->notifikasi->count());

        $this->notifikasi->error('Test 2');
        $this->assertEquals(2, $this->notifikasi->count());

        $this->notifikasi->clear();
        $this->assertEquals(0, $this->notifikasi->count());
    }

    public function testCanCreateWithCustomConfig(): void
    {
        $config = [
            'position' => NotificationPosition::BOTTOM_LEFT,
            'duration' => 3000,
            'theme' => 'dark'
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $this->assertInstanceOf(Notifikasi::class, $notifikasi);
    }

    public function testNotificationWithOptions(): void
    {
        $options = [
            'duration' => 3000,
            'auto_dismiss' => false,
            'custom_data' => ['user_id' => 123]
        ];

        $this->notifikasi->success('Test message', $options);
        $notification = $this->notifikasi->getNotifications()[0];

        $this->assertEquals(3000, $notification->getOption('duration'));
        $this->assertFalse($notification->getOption('auto_dismiss'));
        $this->assertEquals(['user_id' => 123], $notification->getOption('custom_data'));
    }

    public function testRenderMethodReturnsString(): void
    {
        $this->notifikasi->success('Test message');
        $output = $this->notifikasi->render();

        $this->assertIsString($output);
        $this->assertStringContainsString('rzlco-notifikasi', $output);
        $this->assertStringContainsString('Test message', $output);
    }

    public function testRenderClearsNotifications(): void
    {
        $this->notifikasi->success('Test message');
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $this->notifikasi->render();
        $this->assertCount(0, $this->notifikasi->getNotifications());
    }

    public function testRenderEmptyWhenNoNotifications(): void
    {
        $output = $this->notifikasi->render();
        $this->assertEquals('', $output);
    }

    public function testCanUseSessionStorage(): void
    {
        // Mock session functions for testing
        if (!function_exists('session_start')) {
            $this->markTestSkipped('Session functions not available');
        }

        $sessionStorage = new SessionStorage();
        $notifikasi = new Notifikasi($sessionStorage);

        $this->assertInstanceOf(Notifikasi::class, $notifikasi);
    }

    public function testNotificationHasUniqueId(): void
    {
        $this->notifikasi->success('Test 1');
        $this->notifikasi->success('Test 2');

        $notifications = $this->notifikasi->getNotifications();
        $this->assertNotEquals($notifications[0]->getId(), $notifications[1]->getId());
    }

    public function testNotificationHasTimestamp(): void
    {
        $before = time();
        $this->notifikasi->success('Test message');
        $after = time();

        $notification = $this->notifikasi->getNotifications()[0];
        $timestamp = $notification->getTimestamp();

        $this->assertGreaterThanOrEqual($before, $timestamp);
        $this->assertLessThanOrEqual($after, $timestamp);
    }

    public function testCanSerializeNotification(): void
    {
        $this->notifikasi->success('Test message', ['custom' => 'value']);
        $notification = $this->notifikasi->getNotifications()[0];

        $serialized = $notification->toArray();

        $this->assertIsArray($serialized);
        $this->assertArrayHasKey('id', $serialized);
        $this->assertArrayHasKey('level', $serialized);
        $this->assertArrayHasKey('message', $serialized);
        $this->assertArrayHasKey('options', $serialized);
        $this->assertArrayHasKey('timestamp', $serialized);
    }

    public function testCanGetNotificationsByLevel(): void
    {
        $this->notifikasi
            ->success('Success 1')
            ->success('Success 2')
            ->error('Error 1')
            ->warning('Warning 1');

        $notifications = $this->notifikasi->getNotifications();
        $successNotifications = array_filter(
            $notifications,
            fn($notification) => $notification->getLevel() === NotificationLevel::SUCCESS
        );

        $this->assertCount(2, $successNotifications);
    }

    public function testEmptyMessage(): void
    {
        $this->notifikasi->success('');
        $notification = $this->notifikasi->getNotifications()[0];

        $this->assertEquals('', $notification->getMessage());
    }

    public function testLongMessage(): void
    {
        $longMessage = str_repeat('A', 5000);

        $this->notifikasi->success($longMessage);
        $notification = $this->notifikasi->getNotifications()[0];

        $this->assertEquals($longMessage, $notification->getMessage());
    }

    public function testSpecialCharactersInMessage(): void
    {
        $specialMessage = '<script>alert("XSS")</script>';

        $this->notifikasi->success($specialMessage);
        $notification = $this->notifikasi->getNotifications()[0];

        $this->assertEquals($specialMessage, $notification->getMessage());
    }

    public function testUnicodeCharactersInMessage(): void
    {
        $unicodeMessage = 'رسالة تجريبية مع الرموز التعبيرية 😀';

        $this->notifikasi->success($unicodeMessage);
        $notification = $this->notifikasi->getNotifications()[0];

        $this->assertEquals($unicodeMessage, $notification->getMessage());
    }

    public function testFluentInterface(): void
    {
        $result = $this->notifikasi
            ->success('Success message')
            ->error('Error message')
            ->warning('Warning message')
            ->info('Info message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(4, $this->notifikasi->getNotifications());
    }

    public function testDefaultStorage(): void
    {
        $notifikasi = new Notifikasi();
        $this->assertInstanceOf(Notifikasi::class, $notifikasi);

        $notifikasi->success('Test message');
        $this->assertCount(1, $notifikasi->getNotifications());
    }

    public function testCustomBackgroundOpacity(): void
    {
        $config = [
            'background_opacity' => 0.7,
            'background_blur' => 30
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('background: rgba(255, 255, 255, 0.7)', $output);
        $this->assertStringContainsString('backdrop-filter: blur(30px)', $output);
    }

    public function testTimeDisplayConfiguration(): void
    {
        $config = [
            'show_time' => true,
            'time_format' => '24'
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('rzlco-notifikasi-time', $output);
    }

    public function testAnimationDurationConfiguration(): void
    {
        $config = [
            'animation_duration' => 500
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('transition: all 500ms', $output);
    }

    public function testCloseButtonStyling(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('rzlco-notifikasi-close', $output);
        $this->assertStringContainsString('border-radius: 50%', $output);
        $this->assertStringContainsString('background: rgba(128, 128, 128, 0.2)', $output);
    }

    public function testEnhancedAnimations(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('transform: translateX(100%) scale(0.95)', $output);
        $this->assertStringContainsString('transform: translateX(0) scale(1)', $output);
    }

    public function testStaggeredAnimationEffect(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        $notifikasi->success('First message');
        $notifikasi->error('Second message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('setTimeout', $output);
        $this->assertStringContainsString('requestAnimationFrame', $output);
    }

    public function testTimeFormatConfiguration(): void
    {
        $config = [
            'show_time' => true,
            'time_format' => '12'
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        // Cek format 12 jam (harus ada AM/PM)
        $this->assertMatchesRegularExpression('/\\d{1,2}:\\d{2} (AM|PM)/', $output);

        $config24 = [
            'show_time' => true,
            'time_format' => '24'
        ];
        $notifikasi24 = new Notifikasi(new ArrayStorage(), $config24);
        $notifikasi24->success('Test message');
        $output24 = $notifikasi24->render();
        // Cek format 24 jam (tidak ada AM/PM)
        $this->assertMatchesRegularExpression('/\\d{2}:\\d{2}/', $output24);
        $this->assertDoesNotMatchRegularExpression('/AM|PM/', $output24);
    }

    public function testBackgroundBlurConfiguration(): void
    {
        $config = [
            'background_blur' => 40
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('backdrop-filter: blur(40px)', $output);
    }

    public function testIOSLikeBackground(): void
    {
        $config = [
            'background_opacity' => 0.8,
            'background_blur' => 25
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('background: rgba(255, 255, 255, 0.8)', $output);
        $this->assertStringContainsString('backdrop-filter: blur(25px)', $output);
    }

    public function testNotificationWithTimeDisplay(): void
    {
        $config = [
            'show_time' => true,
            'time_format' => '24'
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $notification = $notifikasi->getNotifications()[0];
        $this->assertTrue($notification->getOption('show_time'));
        $this->assertEquals('24', $notification->getOption('time_format'));
    }

    public function testNotificationWithoutTimeDisplay(): void
    {
        $config = [
            'show_time' => false
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test message');

        $notification = $notifikasi->getNotifications()[0];
        $this->assertFalse($notification->getOption('show_time'));
    }

    public function testEnhancedHoverEffects(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('transform: translateY(-2px) scale(1.02)', $output);
    }

    public function testCloseButtonInteraction(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('transform: scale(1.1)', $output);
        $this->assertStringContainsString('transform: scale(0.95)', $output);
    }

    public function testMobileResponsiveAnimations(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('@media (max-width: 640px)', $output);
        $this->assertStringContainsString('transform: none !important', $output);
    }

    public function testAccessibilityAnimations(): void
    {
        $notifikasi = new Notifikasi(new ArrayStorage());
        $notifikasi->success('Test message');

        $output = $notifikasi->render();
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $output);
        $this->assertStringContainsString('transition: none', $output);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up any session data if needed
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}