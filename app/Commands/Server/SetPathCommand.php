<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class SetPathCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:path {path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the global server path';

    protected $path;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->addUsage('~/fivem/servers');
        $this->addUsage('~/servers/fivem');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers, $settings) = $this->getConfig();

        $path = $this->argument('path');

        if (empty($path) && ! isset($settings['server-path'])) {
            $path = $this->ask('Path');
        }

        if (isset($settings['server-path'])) {
            $this->info('Current Path: '.$settings['server-path']);
            exit;
        }

        $this->path = realpath(str_replace('~', $_SERVER['HOME'], $path));

        $this->checkDirectory();

        $settings['server-path'] = $this->path;

        $this->saveSettings($settings);

        $this->info('Global server path set!');
    }

    protected function checkDirectory()
    {
        if (! is_dir($this->path)) {
            $this->error('That directory does not exist!');
            exit;
        }
    }
}
