<?php

namespace App\Commands\Server;

use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class FixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Scheduled] Fixes crashed servers';

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
            exit;
        }

        $status = $this->serverStatus();
        $serverStarted = false;
        foreach ($servers as $name => $server) {
            if ($server['status'] && ! in_array($name, $status)) {
                $serverStarted = true;
                $this->info($name.' was down, restarting.');
                $this->call('server:start', ['name' => $name, '-q' => true]);
            }
        }

        if (! $serverStarted) {
            $this->info('No servers were down!');
        }
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(static::class)->everyMinute();
    }

    protected function serverStatus()
    {
        $status = [];
        exec("ps auxw | grep -i fivem- | grep -v grep | awk '{print $13}'", $status);

        return str_replace('fivem-', '', $status);
    }
}
