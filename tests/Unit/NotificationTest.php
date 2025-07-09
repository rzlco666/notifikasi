<?php

namespace Rzlco\Notifikasi\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Rzlco\Notifikasi\Notification;
use Rzlco\Notifikasi\Enums\NotificationLevel;

class NotificationTest extends TestCase
{
    public function testCanCreateNotificationInstance(): void
    {
        $notification = new Notification(
            NotificationLevel::SUCCESS,
            'Test Title',
            'Test Message'
        );

        $this->assertInstanceOf(Notification::class, $notification);
    }

    public function testNotificationHasCorrectProperties(): void
    {
        $notification = new Notification(
            NotificationLevel::SUCCESS,
            'Test Title',
            'Test Message'
        );

        $this->assertEquals('Test Message', $notification->getMessage());
        $this->assertEquals('Test Title', $notification->getTitle());
        $this->assertEquals(NotificationLevel::SUCCESS, $notification->getLevel());
    }

    public function testNotificationHasUniqueId(): void
    {
        $notification1 = new Notification(NotificationLevel::SUCCESS, 'Title 1', 'Message 1');
        $notification2 = new Notification(NotificationLevel::ERROR, 'Title 2', 'Message 2');

        $this->assertNotEquals($notification1->getId(), $notification2->getId());
        $this->assertIsString($notification1->getId());
        $this->assertIsString($notification2->getId());
    }

    public function testNotificationHasTimestamp(): void
    {
        $before = time();
        $notification = new Notification(NotificationLevel::INFO, 'Title', 'Message');
        $after = time();

        $timestamp = $notification->getTimestamp();
        $this->assertGreaterThanOrEqual($before, $timestamp);
        $this->assertLessThanOrEqual($after, $timestamp);
    }

    public function testNotificationWithOptions(): void
    {
        $options = [
            'duration' => 3000,
            'closable' => false,
            'data' => ['custom' => 'value']
        ];

        $notification = new Notification(
            NotificationLevel::WARNING,
            'Test Title',
            'Test Message',
            $options
        );

        $this->assertEquals(3000, $notification->getOption('duration'));
        $this->assertFalse($notification->getOption('closable'));
        $this->assertEquals(['custom' => 'value'], $notification->getOption('data'));
    }

    public function testNotificationDefaultOptions(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', 'Message');

        $this->assertEquals([], $notification->getOptions());
    }

    public function testCanSetAndGetOptions(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', 'Message');
        
        $notification->setOption('duration', 7000);
        $this->assertEquals(7000, $notification->getOption('duration'));
    }

    public function testCanCheckIfOptionExists(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', 'Message', ['test' => 'value']);
        
        $this->assertTrue($notification->hasOption('test'));
        $this->assertFalse($notification->hasOption('nonexistent'));
    }

    public function testCanSerializeToArray(): void
    {
        $options = [
            'duration' => 3000,
            'closable' => false,
            'data' => ['custom' => 'value']
        ];

        $notification = new Notification(
            NotificationLevel::ERROR,
            'Test Title',
            'Test Message',
            $options
        );

        $array = $notification->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('level', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('message', $array);
        $this->assertArrayHasKey('options', $array);
        $this->assertArrayHasKey('timestamp', $array);

        $this->assertEquals('Test Title', $array['title']);
        $this->assertEquals('Test Message', $array['message']);
        $this->assertEquals('error', $array['level']);
        $this->assertEquals($options, $array['options']);
    }

    public function testCanCreateFromArray(): void
    {
        $data = [
            'id' => 'test-id',
            'level' => 'success',
            'title' => 'Test Title',
            'message' => 'Test Message',
            'options' => ['duration' => 3000],
            'timestamp' => 1234567890
        ];

        $notification = Notification::fromArray($data);
        
        $this->assertEquals('test-id', $notification->getId());
        $this->assertEquals(NotificationLevel::SUCCESS, $notification->getLevel());
        $this->assertEquals('Test Title', $notification->getTitle());
        $this->assertEquals('Test Message', $notification->getMessage());
        $this->assertEquals(['duration' => 3000], $notification->getOptions());
        $this->assertEquals(1234567890, $notification->getTimestamp());
    }

    public function testAllNotificationLevels(): void
    {
        $levels = [
            NotificationLevel::SUCCESS,
            NotificationLevel::ERROR,
            NotificationLevel::WARNING,
            NotificationLevel::INFO
        ];

        foreach ($levels as $level) {
            $notification = new Notification($level, 'Title', 'Message');
            $this->assertEquals($level, $notification->getLevel());
        }
    }

    public function testEmptyMessage(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', '');
        
        $this->assertEquals('', $notification->getMessage());
    }

    public function testLongMessage(): void
    {
        $longMessage = str_repeat('B', 5000);

        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', $longMessage);
        
        $this->assertEquals($longMessage, $notification->getMessage());
    }

    public function testSpecialCharacters(): void
    {
        $specialMessage = 'Test with "quotes" and \'apostrophes\' & ampersands';

        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', $specialMessage);
        
        $this->assertEquals($specialMessage, $notification->getMessage());
    }

    public function testUnicodeCharacters(): void
    {
        $unicodeMessage = 'رسالة تجريبية مع الرموز التعبيرية 😀';

        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', $unicodeMessage);
        
        $this->assertEquals($unicodeMessage, $notification->getMessage());
    }

    public function testComplexDataStructure(): void
    {
        $complexData = [
            'nested' => [
                'array' => [1, 2, 3],
                'object' => ['key' => 'value']
            ],
            'boolean' => true,
            'null' => null,
            'number' => 42.5
        ];

        $notification = new Notification(
            NotificationLevel::INFO,
            'Complex Title',
            'Complex Message',
            $complexData
        );

        $this->assertEquals($complexData, $notification->getOptions());
    }

    public function testToArrayWithComplexData(): void
    {
        $complexData = [
            'array' => [1, 2, 3],
            'object' => ['nested' => 'value'],
            'boolean' => false
        ];

        $notification = new Notification(
            NotificationLevel::WARNING,
            'Complex Title',
            'Complex Message',
            $complexData
        );

        $array = $notification->toArray();
        $this->assertEquals($complexData, $array['options']);
    }

    public function testIdIsConsistentAcrossMethodCalls(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', 'Message');
        
        $id1 = $notification->getId();
        $id2 = $notification->getId();
        
        $this->assertEquals($id1, $id2);
    }

    public function testTimestampIsConsistentAcrossMethodCalls(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', 'Message');
        
        $timestamp1 = $notification->getTimestamp();
        sleep(1);
        $timestamp2 = $notification->getTimestamp();
        
        $this->assertEquals($timestamp1, $timestamp2);
    }

    public function testNotificationLevelToString(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', 'Message');
        $array = $notification->toArray();
        
        $this->assertEquals('success', $array['level']);
    }

    public function testAllLevelsToString(): void
    {
        $levelsToTest = [
            [NotificationLevel::SUCCESS, 'success'],
            [NotificationLevel::ERROR, 'error'],
            [NotificationLevel::WARNING, 'warning'],
            [NotificationLevel::INFO, 'info']
        ];

        foreach ($levelsToTest as [$level, $expectedString]) {
            $notification = new Notification($level, 'Title', 'Message');
            $array = $notification->toArray();
            $this->assertEquals($expectedString, $array['level']);
        }
    }

    public function testCanSetTitle(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Original Title', 'Message');
        
        $notification->setTitle('New Title');
        $this->assertEquals('New Title', $notification->getTitle());
    }

    public function testCanSetMessage(): void
    {
        $notification = new Notification(NotificationLevel::SUCCESS, 'Title', 'Original Message');
        
        $notification->setMessage('New Message');
        $this->assertEquals('New Message', $notification->getMessage());
    }
} 