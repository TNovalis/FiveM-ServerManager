<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;
use Illuminate\Console\Scheduling\Schedule;

class FixCommand extends BaseCommand
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
        list($servers) = $this->getConfig();

        $status = $this->getServerStatus();
        $serverStarted = false;
        foreach ($servers as $name => $server) {
            if ($server['status'] && ! $status[$name]) {
                $serverStarted = true;
                $this->warn($name.' was down, restarting.');
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
}
