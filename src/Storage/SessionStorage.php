<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Storage;

use Rzlco\Notifikasi\Contracts\StorageInterface;
use Rzlco\Notifikasi\Notification;

class SessionStorage implements StorageInterface
{
    private const SESSION_KEY = 'rzlco_notifikasi_notifications';

    public function __construct()
    {
        $this->ensureSessionStarted();
    }

    public function add(Notification $notification): void
    {
        $notifications = $this->getStoredNotifications();
        $notifications[$notification->getId()] = $notification->toArray();
        $this->storeNotifications($notifications);
    }

    public function get(): array
    {
        $storedNotifications = $this->getStoredNotifications();
        $notifications = [];

        foreach ($storedNotifications as $data) {
            $notifications[] = Notification::fromArray($data);
        }

        return $notifications;
    }

    public function clear(): void
    {
        if (function_exists('session')) {
            session()->forget(self::SESSION_KEY);
        } else {
            unset($_SESSION[self::SESSION_KEY]);
        }
    }

    public function has(string $id): bool
    {
        $notifications = $this->getStoredNotifications();
        return array_key_exists($id, $notifications);
    }

    public function remove(string $id): bool
    {
        $notifications = $this->getStoredNotifications();

        if (array_key_exists($id, $notifications)) {
            unset($notifications[$id]);
            $this->storeNotifications($notifications);
            return true;
        }

        return false;
    }

    public function count(): int
    {
        return count($this->getStoredNotifications());
    }

    public function isEmpty(): bool
    {
        return empty($this->getStoredNotifications());
    }

    private function ensureSessionStarted(): void
    {
        // In Laravel context, session is managed by framework
        if (function_exists('session') && session()->isStarted()) {
            return;
        }
        
        // Fallback for native PHP
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getStoredNotifications(): array
    {
        if (function_exists('session')) {
            return session(self::SESSION_KEY, []);
        }
        
        return $_SESSION[self::SESSION_KEY] ?? [];
    }

    private function storeNotifications(array $notifications): void
    {
        if (function_exists('session')) {
            session([self::SESSION_KEY => $notifications]);
        } else {
            $_SESSION[self::SESSION_KEY] = $notifications;
        }
    }
}
