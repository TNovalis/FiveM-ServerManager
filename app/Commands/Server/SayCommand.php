<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class SayCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:say {name?} {message?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message to the server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($server, $serverName) = $this->getServer();

        $message = $this->argument('message');

        if (empty($message)) {
            $message = $this->ask('What is your message');
        }

        $message = addslashes($message);

        if (! $this->getServerStatus()[$serverName]) {
            $this->error('That server is not up!');
            exit;
        }

        exec("screen -S fivem-$serverName -X stuff 'say $message^M'");
    }
}
