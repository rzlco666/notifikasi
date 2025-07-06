<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Rzlco\Notifikasi\Notifikasi success(string $message, array $options = [])
 * @method static \Rzlco\Notifikasi\Notifikasi error(string $message, array $options = [])
 * @method static \Rzlco\Notifikasi\Notifikasi warning(string $message, array $options = [])
 * @method static \Rzlco\Notifikasi\Notifikasi info(string $message, array $options = [])
 * @method static \Rzlco\Notifikasi\Notifikasi add(\Rzlco\Notifikasi\Enums\NotificationLevel $level, string $message, array $options = [])
 * @method static array getNotifications()
 * @method static \Rzlco\Notifikasi\Notifikasi clear()
 * @method static bool hasNotifications()
 * @method static int count()
 * @method static string render()
 *
 * @see \Rzlco\Notifikasi\Notifikasi
 */
class Notifikasi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'notifikasi';
    }
}
