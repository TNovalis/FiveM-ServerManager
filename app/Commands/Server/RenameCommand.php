<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class RenameCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:rename {name? : The name of the server} {new-name? : The new name of the server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename a server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers) = $this->getConfig();

        list($server, $serverName) = $this->getServer();

        $newName = $this->argument('new-name');

        if (empty($newName)) {
            $newName = $this->ask('New server name');
        }

        $newName = str_slug($newName);

        if (! empty($servers[$newName])) {
            $this->warn('A server already exists with that name!');
            exit;
        }

        if (! $this->confirm('Are you sure you want to rename this server?')) {
            $this->info('Canceling.');
            exit;
        }

        $path = $server['path'];
        $newPath = $server['path']."/../$newName";
        $server['path'] = $newPath;

        if (! empty($this->getServerStatus()[$serverName])) {
            $this->warn('Server is being shutdown!');
            exec("screen -XS fivem-$serverName quit");
        }

        exec("mv $path $newPath");

        $this->info("$serverName server renamed to $newName!");

        $servers[$newName] = $server;
        unset($servers[$serverName]);

        $this->saveServers($servers);
    }
}
