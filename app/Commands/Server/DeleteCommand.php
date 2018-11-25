<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class DeleteCommand extends BaseCommand
{
    protected $signature = 'server:delete {name? : The name of the server} {--no-backup : Don\'t backup the server}';

    protected $description = 'Delete a server';

    public function handle()
    {
        $this->runChecks();

        $server = $this->getServer();

        if (! $this->confirm('Are you sure you want to delete this server?')) {
            exit;
        }

        if ($server->pid) {
            $this->warn('Server is being shutdown!');
            $this->call('server:stop', ['name' => $server->name, '-q' => true]);
        }

        if (! $this->option('no-backup')) {
            $this->call('server:backup', ['name' => $server->name]);
        }

        exec("rm -rf $server->path");

        $server->delete();

        $this->info("'$server->name' has been deleted!");
    }
}
