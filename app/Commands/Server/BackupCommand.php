<?php

namespace App\Commands\Server;

use App\Backup;
use App\Server;
use App\Commands\BaseCommand;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;
use Illuminate\Console\Scheduling\Schedule;

class BackupCommand extends BaseCommand
{
    protected $signature = 'server:backup {name? : The name of the server} {--all : Backup all servers}';

    protected $description = 'Backup a server';

    protected $backupPath;

    public function handle()
    {
        $this->runChecks();

        $this->checkDirectory();

        if (! $this->option('all')) {
            $server = $this->getServer();
            $this->createBackup($server);
        } else {
            $servers = Server::all();
            $servers->each(function ($server) {
                $this->createBackup($server);
            });
            $this->setting('backups.last', Carbon::now(), 'datetime');
        }
    }

    protected function checkDirectory()
    {
        $serverPath = $this->setting('server-path');

        if (empty($serverPath)) {
            $this->info('server:path not set, backup will be saved to ~/.fsm');
            $this->backupPath = $_SERVER['HOME'].'.fsm/Backups';
        } else {
            $this->backupPath = $serverPath.'/Backups';
        }

        if (! File::isDirectory($this->backupPath)) {
            File::makeDirectory($this->backupPath);
        }
    }

    /**
     * @param Server $server
     */
    protected function createBackup($server)
    {
        $date = Carbon::now()->format('Y-m-d-H_i');
        $file = "$this->backupPath/$server->name-$date.tar.gz";
        exec("cd $server->path/../; tar -czf $file $server->path 2> /dev/null");
        try {
            (new Backup(['path' => $file, 'server_name' => $server->name]))->save();
        } catch (QueryException $e) {
            // Ignore
        }
        $this->deleteOldBackups($server);
        $this->info("Backup created: $file");
    }

    /**
     * @param Server $server
     */
    protected function deleteOldBackups($server)
    {
        $backupsMax = $this->setting('backups.max');
        $backups = $server->backups->sortBy('created_at');
        if ($backups->count() > $backupsMax) {
            $toRemove = $backups->take($backups->count() - $backupsMax);
            $ids = $toRemove->pluck('id')->toArray();
            $paths = $toRemove->pluck('path')->toArray();
            foreach ($paths as $path) {
                exec("rm $path");
            }
            Backup::destroy($ids);
        }
    }

    public function schedule(Schedule $schedule)
    {
        $schedule->command(static::class, ['--all'])->everyMinute()->when(function () {
            if (! $this->setting('backups.enabled')) {
                return false;
            }
            $backupsEvery = $this->setting('backups.every');
            $backupsLast = $this->setting('backups.last');

            if ($backupsLast->subSecond(1)->lessThan(Carbon::now()->subMinutes($backupsEvery))) {
                return true;
            }

            return false;
        });
    }
}
