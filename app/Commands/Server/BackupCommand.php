<?php

namespace App\Commands\Server;

use App\Commands\BaseCommand;

class BackupCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:backup {name? : The name of the server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup a server';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($server, $serverName) = $this->getServer();

        if (! is_dir($server['path'].'/../Backups')) {
            mkdir($server['path'].'/../Backups');
        }

        $date = date('Y-m-d-H_i');
        $serverPath = $server['path'];
        $backupPath = realpath($server['path'].'/../Backups');
        exec("cd $serverPath/../; tar -czf $backupPath/$serverName-$date.tar.gz $serverName 2> /dev/null");

        $this->info("Backup created: $backupPath/$serverName-$date.tar.gz");
    }
}
