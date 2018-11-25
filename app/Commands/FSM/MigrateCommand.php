<?php

namespace App\Commands\FSM;

use App\Server;
use App\Setting;
use App\Commands\BaseCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class MigrateCommand extends BaseCommand
{
    protected $signature = 'self:migrate';

    protected $description = 'Run after updating FSM to version 1';

    public function handle()
    {
        $path = $_SERVER['HOME'].'/.fsm';
        if (! realpath($path)) {
            File::makeDirectory($path);
            $path = realpath($path);
        }

        if (! File::exists($path.'/fsm.sqlite')) {
            File::put($path.'/fsm.sqlite', '');
        }

        $this->callSilent('migrate', ['--force' => true]);

        $settings = [];
        $servers = [];

        try {
            $settings = json_decode(File::get($path.'/settings.json'), true);
        } catch (FileNotFoundException $e) {
            $this->comment('`settings.json` not found');
        }

        try {
            $servers = json_decode(File::get($path.'/servers.json'), true);
        } catch (FileNotFoundException $e) {
            $this->comment('`servers.json` not found.');
        }

        foreach ($settings as $option => $value) {
            Setting::updateOrCreate(['option' => $option, 'value' => $value]);
        }

        foreach ($servers as $name => $server) {
            Server::updateOrCreate(['name' => $name, 'path' => $server['path'], 'status' => $server['status']]);
        }
    }
}
