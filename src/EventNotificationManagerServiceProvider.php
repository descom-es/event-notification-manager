<?php

namespace Descom\EventNotificationManager;

use Descom\EventNotificationManager\Console\Install;
use Illuminate\Support\ServiceProvider;

class EventNotificationManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'event_notification_manager');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('event_notification_manager.php'),
            ], 'config');

            $this->commands([
                Install::class,
            ]);
        }
    }
}
