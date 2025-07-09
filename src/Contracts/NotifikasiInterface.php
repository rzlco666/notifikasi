<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Contracts;

use Rzlco\Notifikasi\Enums\NotificationLevel;

interface NotifikasiInterface
{
    public function success(string $title, string $message = '', array $options = []): self;

    public function error(string $title, string $message = '', array $options = []): self;

    public function warning(string $title, string $message = '', array $options = []): self;

    public function info(string $title, string $message = '', array $options = []): self;

    public function add(NotificationLevel $level, string $title, string $message = '', array $options = []): self;

    public function getNotifications(): array;

    public function clear(): self;

    public function hasNotifications(): bool;

    public function count(): int;

    public function render(): string;
}
