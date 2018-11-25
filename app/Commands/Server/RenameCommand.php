<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;
use App\Server;

class RenameCommand extends BaseCommand
{
    protected $signature = 'server:rename {name? : The name of the server} {new-name? : The new name of the server}';

    protected $description = 'Rename a server';

    public function handle()
    {
        $this->runChecks();

        $server = $this->getServer();

        $newName = $this->argument('new-name');
        if (empty($newName)) {
            $newName = $this->ask('New server name');
        }

        $newName = str_slug($newName);
        $newPath = realpath("$server->path/..")."/$newName";

        if (! empty(Server::find($newName))) {
            $this->warn('A server already exists with that name!');
            exit;
        }

        if (! $this->confirm('Are you sure you want to rename this server?')) {
            exit;
        }

        if ($server->pid) {
            $this->warn('Server is being shutdown!');
            $this->call('server:stop', ['name' => $server->name, '-q' => true]);
        }

        exec("mv $server->path $newPath");

        $this->info("$server->name has been renamed to $newName");

        $server->name = $newName;
        $server->path = $newPath;
        $server->save();
    }
}
