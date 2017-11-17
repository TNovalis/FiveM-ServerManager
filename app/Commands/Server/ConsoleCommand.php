<?php

namespace App\Commands\Server;

use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ConsoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:console {name? : The name of the server} {--no-warning : Don\'t show the warning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'See the server console';

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

        $serverName = $this->argument('name');

        if (empty($serverName)) {
            $serverName = $this->ask('Which server');
        }

        $serverName = str_slug($serverName);

        if (! isset($servers[$serverName])) {
            $this->error('That server does not exist!');
            exit;
        }

        $server = $servers[$serverName];

        $status = [];
        exec("ps auxw | grep -i fivem- | grep -v grep | awk '{print $13}'", $status);
        $status = str_replace('fivem-', '', $status);

        if (! $server['status'] && ! in_array($serverName, $status)) {
            $this->error('That server is not up!');
            exit;
        }

        if ($server['status'] && ! in_array($serverName, $status)) {
            $this->warn("$serverName may have crashed!");
            if ($this->confirm('Do you want to put it back up?')) {
                $this->call('server:start', ['name' => $serverName, '-q' => true]);
            } else {
                $server['status'] = false;
                $servers[$serverName] = $server;
                Storage::put('servers.json', json_encode($servers));
            }
            exit;
        }

        if (! $this->option('no-warning')) {
            $this->warn('Remember to do [CTRL+A, D] to close the console or you will crash the server!');
            sleep(2);
        }

        system("screen -r fivem-$serverName");
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
