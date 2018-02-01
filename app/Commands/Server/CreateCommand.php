<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class CreateCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:create {name? : The name of the server} {path? : The path to the server, not required if global path is set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a server';

    protected $serverName;

    protected $path;

    protected $servers;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers, $settings) = $this->getConfig();

        $this->servers = $servers;
        $this->settings = $settings;

        $name = $this->argument('name');
        $path = $this->argument('path');

        if (! empty($settings['server-path']) && empty($path)) {
            $path = $settings['server-path'];
        }

        if (empty($name)) {
            $name = $this->ask('Server name');
        }

        if (empty($path)) {
            $path = $this->ask('Server path');
        }

        $this->serverName = str_slug($name);
        $this->path = realpath(str_replace('~', $_SERVER['HOME'], $path));

        $this->checkDirectory();

        $this->downloadFiles();

        $this->setPermissions();

        $servers[$this->serverName] = ['path' => "$this->path/$this->serverName", 'status' => false];

        $this->saveServers($servers);

        $this->info('Server created.');

        $this->warn("Path: $this->path/$this->serverName");
    }

    protected function checkDirectory()
    {
        if (! is_dir($this->path)) {
            $this->warn('The server root directory does not exist!');
            exit;
        }

        if (isset($this->servers[$this->serverName])) {
            $this->warn('That server already exists!');
            exit;
        }

        if (empty(realpath($this->path.'/'.$this->serverName))) {
            mkdir($this->path.'/'.$this->serverName);
        }

        if (! (count(scandir(realpath($this->path.'/'.$this->serverName))) == 2)) {
            $this->warn('That directory is not empty!');
            exit;
        }
    }

    protected function downloadFiles()
    {
        exec("cd $this->path; git clone https://github.com/citizenfx/cfx-server-data.git -q $this->serverName --depth=1; cd $this->serverName; rm -rf .git");

        copy(__DIR__.'/'.'../stubs/server.cfg.stub', "$this->path/$this->serverName/server.cfg");
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
