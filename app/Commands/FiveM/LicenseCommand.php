<?php

namespace App\Commands\FiveM;

use App\Commands\BaseCommand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class LicenseCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fivem:license {license? : Your license key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set your server license key - https://keymaster.fivem.net';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers, $settings) = $this->getConfig();

        $license = $this->argument('license');

        if (empty($license)) {
            $license = $this->ask('License');
        }

        $settings['license'] = $license;
        Storage::put('settings.json', json_encode($settings));

        $this->info('Server license key has been set!');
    }
}
