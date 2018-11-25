<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class PathCommand extends BaseCommand
{
    protected $signature = 'server:path {path?} {--s|set}';

    protected $description = 'Set the global server path';

    protected $path;

    public function handle()
    {
        $this->runChecks();

        $path = $this->argument('path');
        $set = $this->option('set');

        if (($this->setting('server-path') && ! $set) && empty($path)) {
            $this->info('Current Path: '.$this->setting('server-path'));

            return 0;
        }

        if (empty($path)) {
            $path = $this->ask('Server Path');
        }

        $this->path = realpath(str_replace('~', $_SERVER['HOME'], $path));

        $this->checkDirectory();

        $this->setting('server-path', $this->path);
        $this->info('Global server path set!');

        $this->scanDirectory();

        return 1;
    }

    public function checkDirectory()
    {
        if (! $this->path) {
            $this->error('That directory does not exist!');
            exit;
        }
    }

    public function scanDirectory()
    {
        if (! (count(scandir($this->path)) == 2) && $this->confirm('Would you like to check for servers and add them?', true)) {
            $this->call('server:find');
        }
    }
}
