<?php

namespace Descom\EventNotificationManager\Tests\Unit;

use Descom\EventNotificationManager\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallTest extends TestCase
{
    /** @test */
    public function theInstallCommandCopiesTheConfiguration()
    {
        // make sure we're starting from a clean state
        if (File::exists(config_path('event_notification_manager.php'))) {
            unlink(config_path('event_notification_manager.php'));
        }

        $this->assertFalse(File::exists(config_path('event_notification_manager.php')));

        Artisan::call('event_notification_manager:install');

        $this->assertTrue(File::exists(config_path('event_notification_manager.php')));
    }

    public function when_a_config_file_is_present_users_can_choose_to_not_overwrite_it()
    {
        // Given we have already have an existing config file
        File::put(config_path('event_notification_manager.php'), 'test contents');
        $this->assertTrue(File::exists(config_path('event_notification_manager.php')));

        // When we run the install command
        $command = $this->artisan('event_notification_manager:install');

        // We expect a warning that our configuration file exists
        $command->expectsConfirmation(
            'Config file already exists. Do you want to overwrite it?',
            // When answered with "no"
            'no'
        );

        // We should see a message that our file was not overwritten
        $command->expectsOutput('Existing configuration was not overwritten');

        // Assert that the original contents of the config file remain
        $this->assertEquals('test contents', file_get_contents(config_path('event_notification_manager.php')));

        // Clean up
        unlink(config_path('event_notification_manager.php'));
    }

    /** @test */
    public function whenAConfigFileIsPresentUsersCanChooseToDoOverwriteIt()
    {
        // Given we have already have an existing config file
        File::put(config_path('event_notification_manager.php'), 'test contents');
        $this->assertTrue(File::exists(config_path('event_notification_manager.php')));

        // When we run the install command
        $command = $this->artisan('event_notification_manager:install');

        // We expect a warning that our configuration file exists
        $command->expectsConfirmation(
            'Config file already exists. Do you want to overwrite it?',
            // When answered with "yes"
            'yes'
        );

        // execute the command to force override
        $command->execute();

        $command->expectsOutput('Overwriting configuration file...');

        // Assert that the original contents are overwritten
        $this->assertEquals(
            file_get_contents(__DIR__.'/../../config/config.php'),
            file_get_contents(config_path('event_notification_manager.php'))
        );

        // Clean up
        unlink(config_path('event_notification_manager.php'));
    }
}
