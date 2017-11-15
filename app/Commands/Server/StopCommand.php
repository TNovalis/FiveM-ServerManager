<?php

namespace App\Commands\Server;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class StopCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop a server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
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

        exec("screen -XS fivem-$serverName quit");

        $server['status'] = false;
        $servers[$serverName] = $server;

        Storage::put('servers.json', json_encode($servers));

        $this->info("The $serverName server has been stopped.");
    }
}
