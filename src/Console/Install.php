<?php

namespace Descom\EventNotificationManager\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Install extends Command
{
    protected $signature = 'event_notification_manager:install';

    protected $description = 'Install package EventNotificationManager';

    public function handle()
    {
        $this->info('Installing package EventNotificationManager...');

        $this->info('Publishing configuration...');

        if (! $this->configExists('event_notification_manager.php')) {
            $this->publishConfiguration();
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }

        $this->info('Installed package EventNotificationManager');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $this->info('Overwriting configuration file...');

        $params = [
            '--provider' => "Descom\EventNotificationManager\EventNotificationManagerServiceProvider",
            '--tag' => 'config',
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);

        $this->info('Published configuration');
    }
}
