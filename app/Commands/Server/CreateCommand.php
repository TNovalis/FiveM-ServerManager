<?php

namespace App\Commands\Server;

use App\Server;
use App\Commands\BaseCommand;

class CreateCommand extends BaseCommand
{
    protected $signature = 'server:create {name? : The name of the server} {path? : The path to the server (not required)}';

    protected $description = 'Create a server';

    protected $path;

    protected $serverName;

    protected $serverNameSlug;

    public function handle()
    {
        $this->runChecks();

        $server_path = $this->setting('server-path');

        $name = $this->argument('name');
        $path = $this->argument('path');

        if (empty($name)) {
            $name = $this->ask('Server Name');
        }

        if (empty($path) && ! isset($server_path)) {
            $path = $this->ask('Server Path');
        } elseif (empty($path)) {
            $path = $server_path;
        }

        $this->serverName = $name;
        $this->serverNameSlug = str_slug($name);
        $this->path = realpath(str_replace('~', $_SERVER['HOME'], $path));

        $this->checkDirectory();

        $this->downloadFiles();

        $this->setPermissions();

        $server = new Server([
            'name' => $this->serverNameSlug,
            'path' => $this->path.'/'.$this->serverNameSlug,
        ]);

        $server->save();

        $this->info("Server $this->serverName created.");

        $this->comment("Path: $server->path");
    }

    protected function checkDirectory()
    {
        if (! $this->path) {
            $this->warn('The server root directory does not exist!');
            exit;
        }

        if (! empty(Server::find($this->serverNameSlug))) {
            $this->warn('That server already exists!');
            exit;
        }

        if (! (realpath($this->path.'/'.$this->serverNameSlug))) {
            mkdir($this->path.'/'.$this->serverNameSlug);
        }

        if (! (count(scandir(realpath($this->path.'/'.$this->serverNameSlug))) == 2)) {
            $this->warn('That directory is not empty!');
            exit;
        }
    }

    protected function downloadFiles()
    {
        exec("cd $this->path; git clone https://github.com/citizenfx/cfx-server-data.git -q $this->serverNameSlug --depth=1; cd $this->serverNameSlug; rm -rf .git");
        copy(__DIR__.'/'.'../stubs/server.cfg.stub', "$this->path/$this->serverNameSlug/server.cfg");
    }

    protected function checkFiles()
    {
        $files = [
            'server.cfg',
        ];
        foreach ($files as $file) {
            if (! file_exists("$this->path/$file")) {
                $this->error('An error occurred, try again later.');
                exit;
            }
        }
    }

    protected function setPermissions()
    {
        exec("cd $this->path/$this->serverName; chmod -R 771 ./");
    }
}
