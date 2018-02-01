<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class StartCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:start {name? : The name of the server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a server';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        list($servers, $settings) = $this->getConfig();

        list($server, $serverName) = $this->getServer();

        $fivemPath = $settings['fivem-path'];
        $serverPath = $server['path'];

        if (! empty($this->getServerStatus()[$serverName])) {
            $this->warn('That server is already up!');
            exit;
        }

        $key = '';

        if (isset($settings['license'])) {
            $key = '+set sv_licenseKey "'.$settings['license'].'"';
        }

        exec("cd $serverPath; screen -dmS fivem-$serverName $fivemPath/run.sh +exec $serverPath/server.cfg $key");

        $server['status'] = true;
        $servers[$serverName] = $server;

        $this->saveServers($servers);

        $this->info("The '$serverName' server has been started.");
    }
}
