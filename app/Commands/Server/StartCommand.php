<?php

namespace App\Commands\Server;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class StartCommand extends Command
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
        try {
            $settings = json_decode(Storage::get('settings.json'), true);
            $servers = json_decode(Storage::get('servers.json'), true);
        } catch (FileNotFoundException $e) {
            $this->error('FiveM is not installed! Please run server:install');
            exit;
        }

        $serverName = $this->argument('name');

        if (empty($settings['fivem-path'])) {
            $this->error('FiveM in not installed! Please run fivem:install');
            exit;
        }

        if (empty($serverName)) {
            $serverName = $this->ask('Which server');
        }

        $serverName = str_slug($serverName);

        $server = $servers[$serverName];

        if (empty($server)) {
            $this->error('That server does not exist!');
            exit;
        }

        $fivemPath = $settings['fivem-path'];
        $serverPath = $server['path'];

        exec("cd $serverPath; screen -dmS fivem-$serverName $fivemPath/run.sh +exec $serverPath/server.cfg");

        $server['status'] = true;
        $servers[$serverName] = $server;

        Storage::put('servers.json', json_encode($servers));

        $this->info("The $serverName server has been started.");
    }
}
