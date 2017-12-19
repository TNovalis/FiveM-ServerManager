<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class ListCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:list {--path : Show path in the table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the servers and their status';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers) = $this->getConfig();

        $includePath = $this->option('path');

        $headers = ['Server', 'Status'];

        if ($includePath) {
            $headers[] = 'Path';
        }

        $data = [];
        $status = $this->getServerStatus();

        foreach ($servers as $name => $sData) {
            $data[$name] = [];
            $data[$name]['Server'] = $name;
            if ($sData['status'] && ! $status[$name]) {
                $this->promptServerCrashed($name);
            }
            $sData['status'] = $this->getServerStatus()[$name];
            if ($sData['status']) {
                $data[$name]['Status'] = '<info>UP</info>';
            } else {
                $data[$name]['Status'] = '<comment>DOWN</comment>';
            }
            if ($includePath) {
                $data[$name]['Path'] = $sData['path'];
            }
            $servers[$name] = $sData;
        }

        $this->table($headers, $data);
    }
}
