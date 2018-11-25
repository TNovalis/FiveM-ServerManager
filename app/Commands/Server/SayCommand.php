<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class SayCommand extends BaseCommand
{
    protected $signature = 'server:say {name?} {message?}';

    protected $description = 'Send a message to the server';

    public function handle()
    {
        $this->runChecks();

        $server = $this->getServer();

        $message = $this->argument('message');

        if (empty($message)) {
            $message = $this->ask('What\'s your message');
        }

        $message = addslashes($message);

        if (! $server->pid) {
            $this->warn('That server is not up!');
            exit;
        }

        exec("screen -S fivem-$server->name -X stuff 'say $message^M'");
    }
}
