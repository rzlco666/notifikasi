<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Storage;

use Rzlco\Notifikasi\Contracts\StorageInterface;
use Rzlco\Notifikasi\Notification;

class ArrayStorage implements StorageInterface
{
    private array $notifications = [];

    public function add(Notification $notification): void
    {
        $this->notifications[$notification->getId()] = $notification;
    }

    public function get(): array
    {
        return array_values($this->notifications);
    }

    public function clear(): void
    {
        $this->notifications = [];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->notifications);
    }

    public function remove(string $id): bool
    {
        if ($this->has($id)) {
            unset($this->notifications[$id]);
            return true;
        }

        return false;
    }

    public function count(): int
    {
        return count($this->notifications);
    }

    public function isEmpty(): bool
    {
        return empty($this->notifications);
    }
}
