<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;
use App\Server;
use Illuminate\Console\Scheduling\Schedule;

class FixCommand extends BaseCommand
{
    protected $signature = 'server:fix';

    protected $description = 'Fix a crashed server';

    /**
     * Restart the server
     */
    public function handle()
    {
        Server::all()->each(function ($server) {
            if ($server->crashed) {
                $this->warn("$server->name is down, restarting");
                $this->call('server:start', ['name' => $server->name, '-q' => true]);
            }
        });
    }

    public function schedule(Schedule $schedule)
    {
        $schedule->command(static::class)->everyMinute()->when(function () {
            return $this->setting('crash_fix.enabled');
        });
    }
}
