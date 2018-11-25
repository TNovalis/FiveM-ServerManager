<?php

namespace App\Commands\Server;

use App\Server;
use App\Commands\BaseCommand;
use Illuminate\Support\Facades\File;

class FindCommand extends BaseCommand
{
    protected $signature = 'server:find';

    protected $description = 'Finds servers in the server path';

    protected $path;

    public function handle()
    {
        $this->runChecks();

        $path = $this->setting('server-path');

        if (empty($path)) {
            $this->error('Global server path isn\'t set! Please run server:path');
            exit;
        }

        $this->path = $path;

        $serverFolders = $this->findServers();

        $newServerNames = array_keys($serverFolders);
        $serverNames = Server::all()->pluck('name')->toArray();

        $newServerNames = array_diff($newServerNames, $serverNames);

        if (empty($newServerNames)) {
            $this->comment('No new servers found.');
            exit;
        }

        foreach ($newServerNames as $server) {
            (new Server(['name' => $server, 'path' => $serverFolders[$server]]))->save();
        }

        $serverCount = count($newServerNames);
        $plural = str_plural('server', $serverCount);
        $serverList = implode(', ', $newServerNames);
        $this->info("Added $serverCount new $plural: $serverList");
    }

    public function findServers()
    {
        $directories = File::directories($this->path);
        $servers = [];

        if (empty($directories)) {
            return [];
        }

        foreach ($directories as $directory) {
            if (File::isFile($directory.'/server.cfg')) {
                $servers[basename($directory)] = $directory;
            }
        }

        return $servers;
    }
}
