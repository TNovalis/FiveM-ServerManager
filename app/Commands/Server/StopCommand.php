<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class StopCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:stop {name? : The server name} {--no-warning : Don\'t send the warning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop a server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers) = $this->getConfig();

        list($server, $serverName) = $this->getServer();

        if (empty($this->getServerStatus()[$serverName])) {
            $this->warn('That server is not up!');
            exit;
        }

        if (! $this->option('no-warning')) {
            $this->warn('Sending server shutdown message...');
            $this->call('server:say', ['name' => $serverName, 'message' => 'The server is shutting down!', '-q' => true]);
            sleep(3);
        }

        exec("screen -XS fivem-$serverName quit");

        $server['status'] = false;
        $servers[$serverName] = $server;

        $this->saveServers($servers);

        $this->info("The '$serverName' server has been stopped.");
    }
}
