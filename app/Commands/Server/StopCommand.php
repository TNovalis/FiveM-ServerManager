<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class StopCommand extends BaseCommand
{
    protected $signature = 'server:stop {name : The name of the server} {--nw|no-warning : Don\'t show a warning}';

    protected $description = 'Stop a server';

    public function handle()
    {
        $this->runChecks();

        $server = $this->getServer();

        if ($server->crashed) {
            $this->warn('Server crashed, fixing status...');
            $server->status = false;
            $server->save();
            exit;
        }

        if (! $server->pid) {
            $this->warn('That server is not up!');
            exit;
        }

        if (! $this->option('no-warning')) {
            $this->warn('Sending server shutdown message...');
            $this->call('server:say', ['name' => $server->name, 'message' => 'The server is shutting down!', '-q' => true]);
            sleep(3);
        }

        exec("cd $server->path; screen -XS fivem-$server->name quit");

        $server->status = false;
        $server->save();

        $this->info("'$server->name' has been stopped");
    }
}
