<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Contracts;

use Rzlco\Notifikasi\Notification;

interface StorageInterface
{
    public function add(Notification $notification): void;

    public function get(): array;

    public function clear(): void;

    public function has(string $id): bool;

    public function remove(string $id): bool;
}
