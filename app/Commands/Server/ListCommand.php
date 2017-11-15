<?php

namespace App\Commands\Server;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $includePath = $this->option('path');

        $headers = ['Server', 'Status'];

        if ($includePath) {
            $headers[] = 'Path';
        }

        $data = [];

        foreach ($servers as $name => $sData) {
            $data[$name] = [];
            $data[$name]['Server'] = $name;
            if($sData['status']) {
                $data[$name]['Status'] = '<info>UP</info>';
            } else {
                $data[$name]['Status'] = '<comment>DOWN</comment>';
            }
            if($includePath) {
                $data[$name]['Path'] = $sData['path'];
            }
        }

        $this->table($headers, $data);
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
