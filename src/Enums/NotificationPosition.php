<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Enums;

enum NotificationPosition: string
{
    case TOP_RIGHT = 'top-right';
    case TOP_LEFT = 'top-left';
    case TOP_CENTER = 'top-center';
    case BOTTOM_RIGHT = 'bottom-right';
    case BOTTOM_LEFT = 'bottom-left';
    case BOTTOM_CENTER = 'bottom-center';

    public function isTop(): bool
    {
        return in_array($this, [
            self::TOP_RIGHT,
            self::TOP_LEFT,
            self::TOP_CENTER,
        ]);
    }

    public function isBottom(): bool
    {
        return in_array($this, [
            self::BOTTOM_RIGHT,
            self::BOTTOM_LEFT,
            self::BOTTOM_CENTER,
        ]);
    }

    public function isCenter(): bool
    {
        return in_array($this, [
            self::TOP_CENTER,
            self::BOTTOM_CENTER,
        ]);
    }

    public function isLeft(): bool
    {
        return in_array($this, [
            self::TOP_LEFT,
            self::BOTTOM_LEFT,
        ]);
    }

    public function isRight(): bool
    {
        return in_array($this, [
            self::TOP_RIGHT,
            self::BOTTOM_RIGHT,
        ]);
    }
}
