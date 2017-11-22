<?php

namespace App\Commands\Server;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class SayCommand extends Command
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
        try {
            $servers = json_decode(Storage::get('servers.json'), true);
        } catch (FileNotFoundException $e) {
            $this->error('FiveM is not installed! Please run server:install');
            exit;
        }

        $serverName = $this->argument('name');

        if (empty($serverName)) {
            $serverName = $this->ask('Which server');
        }

        $message = $this->argument('message');

        if (empty($message)) {
            $message = $this->ask('What is your question');
        }

        $message = addslashes($message);

        $serverName = str_slug($serverName);

        $server = $servers[$serverName];

        if (empty($server)) {
            $this->error('That server does not exist!');
            exit;
        }

        if (! $server['status']) {
            $this->error('That server is not up!');
            exit;
        }

        exec("screen -S fivem-$serverName -X stuff 'say $message^M'");
    }
}
