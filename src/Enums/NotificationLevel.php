<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Enums;

enum NotificationLevel: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case WARNING = 'warning';
    case INFO = 'info';

    public function getIcon(): string
    {
        return match ($this) {
            self::SUCCESS => '✓',
            self::ERROR => '✕',
            self::WARNING => '⚠',
            self::INFO => 'ℹ',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SUCCESS => '#16a34a',
            self::ERROR => '#dc2626',
            self::WARNING => '#d97706',
            self::INFO => '#2563eb',
        };
    }
}
