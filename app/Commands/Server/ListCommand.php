<?php

namespace App\Commands\Server;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ListCommand extends Command
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
        try {
            $servers = json_decode(Storage::get('servers.json'), true);
        } catch (FileNotFoundException $e) {
            $this->error('FiveM is not installed! Please run server:install');
            exit;
        }

        $status = $this->serverStatus();

        $includePath = $this->option('path');

        $headers = ['Server', 'Status'];

        if ($includePath) {
            $headers[] = 'Path';
        }

        $data = [];

        foreach ($servers as $name => $sData) {
            $data[$name] = [];
            $data[$name]['Server'] = $name;
            if ($sData['status'] && ! in_array($name, $status)) {
                $this->warn("$name may have crashed!");
                if ($this->confirm('Do you want to put it back up?')) {
                    $this->call('server:start', ['name' => $name, '-q' => true]);
                }
            }
            $sData['status'] = in_array($name, $status);
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

        Storage::put('servers.json', json_encode($servers));

        $this->table($headers, $data);
    }

    protected function serverStatus()
    {
        $status = [];
        exec("ps auxw | grep -i fivem- | grep -v grep | awk '{print $13}'", $status);

        return str_replace('fivem-', '', $status);
    }
}
