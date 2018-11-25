<?php

namespace App\Commands\Server;

use App\Server;
use App\Commands\BaseCommand;

class ListCommand extends BaseCommand
{
    protected $signature = 'server:list {--path : Show path in the table}';

    protected $description = 'List the servers and their status';

    public function handle()
    {
        $this->runChecks();

        $includePath = $this->option('path');

        $headers = ['Server', 'Status'];

        if ($includePath) {
            $headers[] = 'Path';
        }

        $data = [];
        Server::all()->each(function ($server) use (&$data, $includePath) {
            $data[$server->name] = [];
            $data[$server->name]['Server'] = $server->name;
            $data[$server->name]['Status'] = $server->pid ? '<info>UP</info>' : '<comment>DOWN</comment>';

            if ($includePath) {
                $data[$server->name]['Path'] = $server->path;
            }
        });

        $this->table($headers, $data);
    }
}
