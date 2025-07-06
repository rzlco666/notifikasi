<?php

declare(strict_types=1);

namespace Rzlco\Notifikasi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Rzlco\Notifikasi\Contracts\NotifikasiInterface;
use Rzlco\Notifikasi\Contracts\StorageInterface;
use Rzlco\Notifikasi\Storage\SessionStorage;

class NotifikasiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/notifikasi.php',
            'notifikasi'
        );

        $this->app->bind(StorageInterface::class, function ($app) {
            return new SessionStorage();
        });

        $this->app->bind(NotifikasiInterface::class, function ($app) {
            return new Notifikasi(
                $app->make(StorageInterface::class),
                config('notifikasi', [])
            );
        });

        $this->app->alias(NotifikasiInterface::class, 'notifikasi');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/notifikasi.php' => config_path('notifikasi.php'),
        ], 'notifikasi-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/notifikasi'),
        ], 'notifikasi-views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'notifikasi');

        $this->registerBladeDirectives();
    }

    private function registerBladeDirectives(): void
    {
        Blade::directive('notifikasi', function () {
            return "<?php echo app('notifikasi')->render(); ?>";
        });

        Blade::directive('notifikasiScripts', function () {
            return "<?php echo view('notifikasi::scripts')->render(); ?>";
        });

        Blade::directive('notifikasiStyles', function () {
            return "<?php echo view('notifikasi::styles')->render(); ?>";
        });
    }

    public function provides(): array
    {
        return [
            NotifikasiInterface::class,
            'notifikasi',
        ];
    }
}
