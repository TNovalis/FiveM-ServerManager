<?php

namespace App\Commands\Server;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class DeleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:delete {name? : The name of the server} {--no-backup : Don\'t backup the server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a server';

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

        $serverName = str_slug($serverName);

        if (! isset($servers[$serverName])) {
            $this->error('That server does not exist!');
            exit;
        }

        if (! $this->confirm('Are you sure you want to delete this server?')) {
            $this->info('Canceling.');
            exit;
        }

        $path = $servers[$serverName]['path'];

        $pid = exec("ps auxw | grep -i fivem-$serverName | grep -v grep | awk '{print $2}'");

        if (! empty($pid)) {
            $this->warn('Server is being shutdown!');
            exec("kill $pid");
        }

        if (! $this->option('no-backup')) {
            $this->call('server:backup', ['name' => $serverName]);
        }

        exec("rm -rf $path");

        $this->info("$serverName server deleted!");

        unset($servers[$serverName]);

        Storage::put('servers.json', json_encode($servers));
    }
}
