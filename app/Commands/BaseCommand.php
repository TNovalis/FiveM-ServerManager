<?php

namespace App\Commands;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

abstract class BaseCommand extends Command
{
    protected function getConfig()
    {
        try {
            $servers = json_decode(Storage::get('servers.json'), true);
            $settings = json_decode(Storage::get('settings.json'), true);
        } catch (FileNotFoundException $e) {
            $this->error('FiveM is not installed! Please run server:install');
            exit;
        }

        if (empty($settings['fivem-path'])) {
            $this->error('FiveM in not installed! Please run fivem:install');
            exit;
        }

        return [
            $servers,
            $settings,
        ];
    }

    protected function getServer()
    {
        list($servers) = $this->getConfig();

        $serverName = $this->argument('name');

        if (empty($serverName)) {
            $serverName = $this->askWithCompletion('Which server', array_keys($servers));
        }

        $serverName = str_slug($serverName);

        if (empty($servers[$serverName])) {
            $this->error('That server does not exist!');
            exit;
        }

        $server = $servers[$serverName];

        return [
            $server,
            $serverName,
        ];
    }

    protected function getServerPid($serverName)
    {
        $status = exec("ps auxw | grep -i fivem-$serverName | grep -v grep | awk '{print $2}'");

        return $status;
    }

    protected function getServerStatus()
    {
        list($servers) = $this->getConfig();
        $status = [];
        exec("ps auxw | grep -i fivem- | grep -v grep | awk '{print $13}'", $status);

        $status = str_replace('fivem-', '', $status);
        $serverStatus = [];

        foreach ($servers as $name => $server) {
            $serverStatus[$name] = in_array($name, $status);
        }

        return $serverStatus;
    }

    protected function promptServerCrashed($serverName)
    {
        list($servers) = $this->getConfig();

        $this->warn("$serverName may have crashed!");
        if ($this->confirm('Do you want to put it back up?')) {
            $this->call('server:start', ['name' => $serverName, '-q' => true]);
        } else {
            $server['status'] = false;
            $servers[$serverName] = $server;
            $this->saveServers($servers);
        }
    }

    protected function saveServers($servers)
    {
        return Storage::put('servers.json', json_encode($servers));
    }

    protected function saveSettings($settings)
    {
        return Storage::put('settings.json', json_encode($settings));
    }
}
