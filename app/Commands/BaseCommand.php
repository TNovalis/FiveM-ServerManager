<?php

namespace App\Commands;

use App\Server;
use App\Setting;
use LaravelZero\Framework\Commands\Command;

abstract class BaseCommand extends Command
{
    protected function setting($option, $value = null, $type = null)
    {
        if ($value === null) {
            return optional(Setting::find($option))->value;
        }

        if ($type === null) {
            $type = gettype($value);
        }

        return Setting::updateOrCreate(['option' => $option], ['value' => $value, 'type' => $type]);
    }

    /**
     * Makes sure FiveM is installed.
     */
    protected function runChecks()
    {
        if (empty($this->setting('fivem-path'))) {
            $this->error('FiveM is not installed! Please run fivem:install');
            exit;
        }
    }

    /**
     * Get the server from the user.
     *
     * @param null $serverName
     *
     * @return Server
     */
    protected function getServer($serverName = null)
    {
        $internal = false;

        if (empty($serverName)) {
            $serverName = $this->argument('name');
        } else {
            $internal = true;
        }

        if (empty($serverName)) {
            $serverName = $this->askWithCompletion('Server Name', Server::all()->pluck('name')->toArray());
        }

        $serverName = str_slug($serverName);

        $server = Server::find($serverName);

        if (empty($server)) {
            if (! $internal) {
                $this->error('That server does not exist!');
                exit;
            }

            return $server;
        }

        return $server;
    }
}
