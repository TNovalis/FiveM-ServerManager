<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class ConsoleCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:console {name? : The name of the server} {--no-warning : Don\'t show the warning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'See the server console';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($server, $serverName) = $this->getServer();

        $status = $this->getServerStatus();

        if (empty($this->getServerStatus()[$serverName])) {
            $this->warn('That server is not up!');
            exit;
        }

        if ($server['status'] && ! $status[$serverName]) {
            $this->promptServerCrashed($serverName);
            exit;
        }

        if (! $this->option('no-warning')) {
            $this->warn('Remember to do [CTRL+A, D] to close the console or you will crash the server!');
            sleep(2);
        }

        system("screen -r fivem-$serverName");
    }
}
