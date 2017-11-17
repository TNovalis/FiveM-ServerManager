<?php

namespace App\Commands\Server;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class BackupCommand extends Command
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
        try {
            $servers = json_decode(Storage::get('servers.json'), true);
        } catch (FileNotFoundException $e) {
            $this->error('FiveM is not installed! Please run server:install');
            exit;
        }

        $serverName = $this->argument('name');

        if (empty($serverName)) {
            $serverName = $this->ask('Which server');
        }

        $serverName = str_slug($serverName);

        $server = $servers[$serverName];

        if (! is_dir($server['path'].'/../Backups')) {
            mkdir($server['path'].'/../Backups');
        }

        $date = date('Y-m-d-H:i');
        $serverPath = $server['path'];
        $backupPath = realpath($server['path'].'/../Backups');
        exec("tar -czf $backupPath/$serverName-$date.tar.gz $serverPath 2> /dev/null");

        $this->info("Backup created: $backupPath/$serverName-$date.tar.gz");
    }
}
