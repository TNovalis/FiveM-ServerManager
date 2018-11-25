<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class StartCommand extends BaseCommand
{
    protected $signature = 'server:start {name? : The name of the server}';

    protected $description = 'Start a server';

    public function handle()
    {
        $this->runChecks();

        $fivem = $this->setting('fivem-path');
        $server = $this->getServer();

        if ($server->pid) {
            $this->warn('That server is already up!');
            exit;
        }

        $startCommand = "screen -dmS fivem-$server->name $fivem/run.sh +exec $server->path/server.cfg";

        if (! empty($license = $this->setting('license'))) {
            $startCommand .= " +set sv_licenseKey $license";
        }

        exec("cd $server->path; $startCommand");

        $started = $server->pid;

        $server->status = $started;
        $server->save();

        if ($started) {
            $this->info("'$server->name' has been started.");
        } else {
            $this->error("'$server->name' could not be started!");
        }
    }
}
