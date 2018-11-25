<?php

namespace App\Commands\Server;

use App\Server;
use App\Commands\BaseCommand;
use Illuminate\Support\Carbon;
use Illuminate\Console\Scheduling\Schedule;

class RestartCommand extends BaseCommand
{
    protected $signature = 'server:restart {name? : The name of the server} {--all : Restart all servers} {--nw|no-warning : Don\'t show a warning}';

    protected $description = 'Restart a server';

    /**
     * Restart the server.
     */
    public function handle()
    {
        if (! $this->option('all')) {
            $server = $this->getServer();
            $this->call('server:stop', ['name' => $server->name, '-nw' => $this->option('no-warning')]);
            $this->call('server:start', ['name' => $server->name]);
        } else {
            Server::all()->each(function ($server) {
                if (! $server->pid) {
                    return;
                }
                $this->setting('restarts.last', Carbon::now()->toDateString());
                $this->call('server:stop', ['name' => $server->name, '-nw' => $this->option('no-warning')]);
                $this->call('server:start', ['name' => $server->name]);
            });
        }
    }

    public function schedule(Schedule $schedule)
    {
        $schedule->command(static::class, ['--all'])->everyMinute()->when(function () {
            if (! $this->setting('restarts.enabled')) {
                return false;
            }
            $restartsLast = $this->setting('restarts.last');
            if (! empty($restartsLast) && Carbon::parse($restartsLast)->addMinutes(5)->greaterThan(Carbon::now())) {
                return false;
            }
            $restartsTime = explode(':', $this->setting('restarts.time'));
            $timeBefore = Carbon::now()->subMinute();
            $timeAfter = Carbon::now()->addMinute();
            $restart = Carbon::now()->setTime($restartsTime[0], $restartsTime[1]);
            if ($restart->between($timeBefore, $timeAfter)) {
                return true;
            }

            return false;
        });
    }
}
