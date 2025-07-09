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
        $result = $this->notifikasi->success('Success Title', 'This is a success message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is a success message', $notification->getMessage());
        $this->assertEquals('Success Title', $notification->getTitle());
        $this->assertEquals(NotificationLevel::SUCCESS, $notification->getLevel());
    }

    public function testCanAddErrorNotification(): void
    {
        $result = $this->notifikasi->error('Error Title', 'This is an error message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is an error message', $notification->getMessage());
        $this->assertEquals('Error Title', $notification->getTitle());
        $this->assertEquals(NotificationLevel::ERROR, $notification->getLevel());
    }

    public function testCanAddWarningNotification(): void
    {
        $result = $this->notifikasi->warning('Warning Title', 'This is a warning message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is a warning message', $notification->getMessage());
        $this->assertEquals('Warning Title', $notification->getTitle());
        $this->assertEquals(NotificationLevel::WARNING, $notification->getLevel());
    }

    public function testCanAddInfoNotification(): void
    {
        $result = $this->notifikasi->info('Info Title', 'This is an info message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(1, $this->notifikasi->getNotifications());

        $notification = $this->notifikasi->getNotifications()[0];
        $this->assertEquals('This is an info message', $notification->getMessage());
        $this->assertEquals('Info Title', $notification->getTitle());
        $this->assertEquals(NotificationLevel::INFO, $notification->getLevel());
    }

    public function testCanAddMultipleNotifications(): void
    {
        $this->notifikasi
            ->success('First Success', 'First success message')
            ->error('First Error', 'First error message')
            ->warning('First Warning', 'First warning message')
            ->info('First Info', 'First info message');

        $this->assertCount(4, $this->notifikasi->getNotifications());
    }

    public function testCanClearNotifications(): void
    {
        $this->notifikasi
            ->success('First Success', 'First success message')
            ->error('First Error', 'First error message');

        $this->assertCount(2, $this->notifikasi->getNotifications());

        $this->notifikasi->clear();
        $this->assertCount(0, $this->notifikasi->getNotifications());
    }

    public function testHasNotifications(): void
    {
        $this->assertFalse($this->notifikasi->hasNotifications());

        $this->notifikasi->success('Test Title', 'Test message');
        $this->assertTrue($this->notifikasi->hasNotifications());

        $this->notifikasi->clear();
        $this->assertFalse($this->notifikasi->hasNotifications());
    }

    public function testCountNotifications(): void
    {
        $this->assertEquals(0, $this->notifikasi->count());

        $this->notifikasi->success('Test 1', 'Message 1');
        $this->assertEquals(1, $this->notifikasi->count());

        $this->notifikasi->error('Test 2', 'Message 2');
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

        $this->notifikasi->success('Test Title', 'Test message', $options);
        $notification = $this->notifikasi->getNotifications()[0];

        $this->assertEquals(3000, $notification->getOption('duration'));
        $this->assertFalse($notification->getOption('auto_dismiss'));
        $this->assertEquals(['user_id' => 123], $notification->getOption('custom_data'));
    }

    public function testRenderMethodReturnsString(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertIsString($output);
        $this->assertStringContainsString('rzlco-notifikasi', $output);
        $this->assertStringContainsString('Test Title', $output);
        $this->assertStringContainsString('Test message', $output);
    }

    public function testRenderClearsNotifications(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
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
        // Skip test if no Laravel environment is available
        $this->markTestSkipped('SessionStorage requires Laravel environment');
    }

    public function testNotificationHasUniqueId(): void
    {
        $this->notifikasi->success('Test 1', 'Message 1');
        $this->notifikasi->success('Test 2', 'Message 2');

        $notifications = $this->notifikasi->getNotifications();
        $this->assertNotEquals($notifications[0]->getId(), $notifications[1]->getId());
    }

    public function testNotificationHasTimestamp(): void
    {
        $before = time();
        $this->notifikasi->success('Test Title', 'Test message');
        $after = time();

        $notification = $this->notifikasi->getNotifications()[0];
        $timestamp = $notification->getTimestamp();

        $this->assertGreaterThanOrEqual($before, $timestamp);
        $this->assertLessThanOrEqual($after, $timestamp);
    }

    public function testCanSerializeNotification(): void
    {
        $options = [
            'duration' => 3000,
            'closable' => false,
            'data' => ['custom' => 'value']
        ];

        $this->notifikasi->success('Test Title', 'Test Message', $options);
        $notification = $this->notifikasi->getNotifications()[0];
        $array = $notification->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test Title', $array['title']);
        $this->assertEquals('Test Message', $array['message']);
        $this->assertEquals('success', $array['level']);
    }

    public function testCanGetNotificationsByLevel(): void
    {
        $this->notifikasi
            ->success('Success Title', 'Success message')
            ->error('Error Title', 'Error message')
            ->warning('Warning Title', 'Warning message')
            ->info('Info Title', 'Info message');

        $notifications = $this->notifikasi->getNotifications();
        $this->assertCount(4, $notifications);

        $levels = array_map(fn($n) => $n->getLevel(), $notifications);
        $this->assertContains(NotificationLevel::SUCCESS, $levels);
        $this->assertContains(NotificationLevel::ERROR, $levels);
        $this->assertContains(NotificationLevel::WARNING, $levels);
        $this->assertContains(NotificationLevel::INFO, $levels);
    }

    public function testEmptyMessage(): void
    {
        $this->notifikasi->success('Title Only', '');
        $notification = $this->notifikasi->getNotifications()[0];
        
        $this->assertEquals('', $notification->getMessage());
        $this->assertEquals('Title Only', $notification->getTitle());
    }

    public function testLongMessage(): void
    {
        $longMessage = str_repeat('A', 5000);
        $this->notifikasi->success('Long Message Title', $longMessage);
        $notification = $this->notifikasi->getNotifications()[0];
        
        $this->assertEquals($longMessage, $notification->getMessage());
    }

    public function testSpecialCharactersInMessage(): void
    {
        $specialMessage = '<script>alert("XSS")</script>';
        $this->notifikasi->success('XSS Test', $specialMessage);
        $notification = $this->notifikasi->getNotifications()[0];
        
        $this->assertEquals($specialMessage, $notification->getMessage());
    }

    public function testUnicodeCharactersInMessage(): void
    {
        $unicodeMessage = 'رسالة تجريبية مع الرموز التعبيرية 😀';
        $this->notifikasi->success('Unicode Test', $unicodeMessage);
        $notification = $this->notifikasi->getNotifications()[0];
        
        $this->assertEquals($unicodeMessage, $notification->getMessage());
    }

    public function testFluentInterface(): void
    {
        $result = $this->notifikasi
            ->success('Success', 'Success message')
            ->error('Error', 'Error message')
            ->warning('Warning', 'Warning message')
            ->info('Info', 'Info message');

        $this->assertInstanceOf(Notifikasi::class, $result);
        $this->assertCount(4, $this->notifikasi->getNotifications());
    }

    public function testDefaultStorage(): void
    {
        $notifikasi = new Notifikasi();
        $this->assertInstanceOf(Notifikasi::class, $notifikasi);
    }

    public function testCustomBackgroundOpacity(): void
    {
        $config = [
            'background_opacity' => 0.5
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test Title', 'Test message');
        $output = $notifikasi->render();

        $this->assertStringContainsString('rgba(255, 255, 255, 0.5)', $output);
    }

    public function testTimeDisplayConfiguration(): void
    {
        $config = [
            'show_time' => true,
            'time_format' => '24'
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test Title', 'Test message');
        $output = $notifikasi->render();

        $this->assertStringContainsString('rzlco-notifikasi-time', $output);
    }

    public function testAnimationDurationConfiguration(): void
    {
        $config = [
            'animation_duration' => 500
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test Title', 'Test message');
        $output = $notifikasi->render();

        $this->assertStringContainsString('animationDuration: 500', $output);
    }

    public function testCloseButtonStyling(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString('rzlco-notifikasi-close', $output);
        $this->assertStringContainsString('Close notification', $output);
    }

    public function testEnhancedAnimations(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString('transition: all', $output);
        $this->assertStringContainsString('cubic-bezier', $output);
    }

    public function testStaggeredAnimationEffect(): void
    {
        $this->notifikasi->success('Test 1', 'Message 1');
        $this->notifikasi->success('Test 2', 'Message 2');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString('index * 100', $output);
    }

    public function testTimeFormatConfiguration(): void
    {
        $config12 = ['time_format' => '12', 'show_time' => true];
        $config24 = ['time_format' => '24', 'show_time' => true];

        $notifikasi12 = new Notifikasi(new ArrayStorage(), $config12);
        $notifikasi24 = new Notifikasi(new ArrayStorage(), $config24);

        $notifikasi12->success('Test Title', 'Test message');
        $output12 = $notifikasi12->render();

        $notifikasi24->success('Test Title', 'Test message');
        $output24 = $notifikasi24->render();

        // Both should contain time display element
        $this->assertStringContainsString('<div class="rzlco-notifikasi-time">', $output12);
        $this->assertStringContainsString('<div class="rzlco-notifikasi-time">', $output24);
        
        // Check that we have different time formats in the actual HTML
        // 12-hour format should have AM/PM, 24-hour should not
        preg_match('/<div class="rzlco-notifikasi-time">([^<]+)<\/div>/', $output12, $matches12);
        preg_match('/<div class="rzlco-notifikasi-time">([^<]+)<\/div>/', $output24, $matches24);
        
        if (isset($matches12[1])) {
            $this->assertMatchesRegularExpression('/\d{1,2}:\d{2}\s+(AM|PM)/', $matches12[1]);
        }
        
        if (isset($matches24[1])) {
            $this->assertMatchesRegularExpression('/\d{2}:\d{2}$/', $matches24[1]);
        }
    }

    public function testBackgroundBlurConfiguration(): void
    {
        $config = [
            'background_blur' => 15
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test Title', 'Test message');
        $output = $notifikasi->render();

        $this->assertStringContainsString('blur(15px)', $output);
    }

    public function testIOSLikeBackground(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString('backdrop-filter', $output);
        $this->assertStringContainsString('-webkit-backdrop-filter', $output);
        $this->assertStringContainsString('rgba(255, 255, 255', $output);
    }

    public function testNotificationWithTimeDisplay(): void
    {
        $config = [
            'show_time' => true
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test Title', 'Test message');
        $output = $notifikasi->render();

        $this->assertStringContainsString('rzlco-notifikasi-time', $output);
    }

    public function testNotificationWithoutTimeDisplay(): void
    {
        $config = [
            'show_time' => false
        ];

        $notifikasi = new Notifikasi(new ArrayStorage(), $config);
        $notifikasi->success('Test Title', 'Test message');
        $output = $notifikasi->render();

        // Should not contain the time element in HTML
        $this->assertStringNotContainsString('<div class="rzlco-notifikasi-time">', $output);
    }

    public function testEnhancedHoverEffects(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString(':hover', $output);
        $this->assertStringContainsString('translateY(-2px)', $output);
    }

    public function testCloseButtonInteraction(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString('closeBtn.closest', $output);
        $this->assertStringContainsString('hideNotification', $output);
    }

    public function testMobileResponsiveAnimations(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString('@media (max-width: 640px)', $output);
        $this->assertStringContainsString('left: 10px', $output);
        $this->assertStringContainsString('right: 10px', $output);
    }

    public function testAccessibilityAnimations(): void
    {
        $this->notifikasi->success('Test Title', 'Test message');
        $output = $this->notifikasi->render();

        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $output);
        $this->assertStringContainsString('transition: none', $output);
    }

    protected function tearDown(): void
    {
        $this->notifikasi->clear();
        parent::tearDown();
    }
}