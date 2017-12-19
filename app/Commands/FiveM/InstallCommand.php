<?php

namespace App\Commands\FiveM;

use App\Commands\BaseCommand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class InstallCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fivem:install {path? : The path to the server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the server files';

    protected $path;

    protected $fxVersionNumber;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        try {
            $settings = json_decode(Storage::get('settings.json'), true);
        } catch (FileNotFoundException $e) {
            $this->call('create:files');
            $settings = [];
        }

        $path = $this->argument('path');

        if (empty($path)) {
            $path = $this->ask('Path');
        }

        $this->path = realpath(str_replace('~', $_SERVER['HOME'], $path));

        $this->checkDirectory();

        $this->downloadFiles();

        $this->checkFiles();

        $this->setPermissions();

        $settings['fivem-path'] = $this->path;
        $settings['fivem-version'] = $this->fxVersionNumber;
        Storage::put('settings.json', json_encode($settings));

        $this->info('FiveM has been downloaded and installed!');
    }

    protected function checkDirectory()
    {
        if (! is_dir($this->path)) {
            $this->error('That directory does not exist!');
            exit;
        }

        if (! (count(scandir(realpath($this->path))) == 2)) {
            $confirm = $this->confirm('That directory is not empty, are you sure?');
        }

        if (isset($confirm) && ($confirm == false)) {
            exit;
        }
    }

    protected function downloadFiles()
    {
        $buildsURL = 'https://runtime.fivem.net/artifacts/fivem/build_proot_linux/master/';

        $newestFXVersion = exec("curl $buildsURL -s | grep '<a href' | tail -1 | awk -F[\>\<] '{print $3}'");

        $this->fxVersionNumber = strtok($newestFXVersion, '-');

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
