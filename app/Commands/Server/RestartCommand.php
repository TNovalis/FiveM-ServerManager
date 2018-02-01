<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class RestartCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:restart {name?} {--nw|no-warning : Don\'t show a warning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart a server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($server, $serverName) = $this->getServer();

        $this->call('server:stop', ['name' => $serverName, '-nw' => $this->option('no-warning')]);
        $this->call('server:start', ['name' => $serverName]);
    }
}
