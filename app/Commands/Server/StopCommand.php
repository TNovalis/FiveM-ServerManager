<?php

namespace App\Commands\Server;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class StopCommand extends Command
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

        if (! isset($server)) {
            $this->error('That server does not exist!');
            exit;
        }

        if (! $server['status']) {
            $this->error('That server is not up!');
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

        Storage::put('servers.json', json_encode($servers));

        $this->info("The $serverName server has been stopped.");
    }
}
