<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;
use App\Setting;

class ConsoleCommand extends BaseCommand
{
    protected $signature = 'server:console {name? : The name of the server} {--no-warning : Don\'t show usage warnings}';

    protected $description = 'Open the server console';

    protected $path;

    protected $fxVersionNumber;

    /**
     * Open the server console
     */
    public function handle()
    {
        $this->runChecks();

        $server = $this->getServer();

        if (! $server->pid) {
            $this->warn('That server is not up!');
            exit;
        }

        if ($server->crashed) {
            if (Setting::find('crash_fix.enabled')) {
                $this->info('The server crashed, restarting it.');
                $this->call('server:start', ['name' => $server->name]);
            } else {
                if ($this->confirm('The server crashed, would you like to restart it?')) {
                    $this->call('server:start', ['name' => $server->name]);
                }
            }
        }

        if (! $this->option('no-warning')) {
            $this->warn('Remember to do [CTRL+A, D] to close the console or you will crash the server!');
            sleep(2);
        }

        system("screen -r fivem-$server->name");
    }
}
