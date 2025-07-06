<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Contracts;

use Rzlco\Notifikasi\Enums\NotificationLevel;

interface NotifikasiInterface
{
    public function success(string $message, array $options = []): self;

    public function error(string $message, array $options = []): self;

    public function warning(string $message, array $options = []): self;

    public function info(string $message, array $options = []): self;

    public function add(NotificationLevel $level, string $message, array $options = []): self;

    public function getNotifications(): array;

    public function clear(): self;

    public function hasNotifications(): bool;

    public function count(): int;

    public function render(): string;
}
