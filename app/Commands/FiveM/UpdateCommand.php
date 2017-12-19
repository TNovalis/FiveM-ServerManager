<?php

namespace App\Commands\FiveM;

use App\Commands\BaseCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class UpdateCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fivem:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the server files';

    protected $path;

    protected $fxVersionNumber;

    protected $version;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        list($servers, $settings) = $this->getConfig();

        $this->path = $settings['fivem-path'];
        $this->version = $settings['fivem-version'];

        if (empty($this->path)) {
            $this->error('FiveM is not installed! Please run server:install');
            exit;
        }

        $this->downloadFiles();

        $this->checkFiles();

        $this->setPermissions();

        $settings['fivem-version'] = $this->fxVersionNumber;
        $this->saveServers($servers);

        $this->info('FiveM has been updated!');
    }

    protected function downloadFiles()
    {
        $buildsURL = 'https://runtime.fivem.net/artifacts/fivem/build_proot_linux/master/';

        $newestFXVersion = exec("curl $buildsURL -s | grep '<a href' | tail -1 | awk -F[\>\<] '{print $3}'");

        $this->fxVersionNumber = strtok($newestFXVersion, '-');

        if ($this->version == $this->fxVersionNumber) {
            $this->info('FiveM is already up-to-date!');
            exit;
        }

        $newestFXLink = $buildsURL.$newestFXVersion.'fx.tar.xz';

        exec("cd $this->path; curl -sO $newestFXLink; tar xf fx.tar.xz 2> /dev/null; rm fx.tar.xz");
    }

    protected function checkFiles()
    {
        $files = [
            'run.sh',
            'proot',
        ];

        foreach ($files as $file) {
            if (! file_exists("$this->path/$file")) {
                $this->error('An error occurred, try again later.');
                exit;
            }
        }
    }

    protected function setPermissions()
    {
        exec("cd $this->path; chmod -R 771 ./");
    }
}
