<?php

namespace App\Commands\FiveM;

use App\Commands\BaseCommand;
use Illuminate\Support\Facades\File;
use Weidner\Goutte\GoutteFacade;

class InstallCommand extends BaseCommand
{
    protected $signature = 'fivem:install {path?}';

    protected $description = 'Install the FiveM server files';

    protected $path;

    protected $fxVersionNumber;

    /**
     * Install the FiveM server files
     */
    public function handle()
    {
        if (! empty($this->setting('fivem-path')) && ! $this->confirm('FiveM is already installed, do you want to continue?')) {
            exit;
        }

        $path = $this->argument('path');

        if (empty($path)) {
            $path = $this->ask('Where do you want to install the FiveM server?');
        }

        $this->path = realpath(str_replace('~', $_SERVER['HOME'], $path));

        $this->checkDirectory();

        $this->downloadFiles();

        $this->setPermissions();

        $this->setting('fivem-path', $this->path);
        $this->setting('fivem-version', $this->fxVersionNumber);
        $this->info('FiveM has been downloaded and installed!');
    }

    protected function checkDirectory()
    {
        if (! $this->path) {
            $this->error('That directory does not exist!');
            exit;
        }

        if (count(File::files($this->path)) && ! $this->confirm('That directory is not empty, are you sure?')) {
            exit;
        }
    }

    protected function downloadFiles()
    {
        $buildsURL = 'https://runtime.fivem.net/artifacts/fivem/build_proot_linux/master/';

        $crawler = GoutteFacade::request('GET', $buildsURL);
        $newestBuild = collect($crawler->filter('a')->each(function ($n) use ($buildsURL) {
            $link = $n->attr('href');
            if (! is_numeric(substr($link, 0, 3))) {
                return null;
            }
            $version = explode('-', trim($link, '/'))[0];

            return ['version' => intval($version), 'link' => $buildsURL.$link.'fx.tar.xz'];
        }))->filter()->sortByDesc('version')->first();
        $this->fxVersionNumber = $newestBuild['version'];
        $link = $newestBuild['link'];

        $this->comment('Downloading and extracting files...');
        exec("cd $this->path; curl -sO $link; tar xf fx.tar.xz 2> /dev/null; rm fx.tar.xz");
    }

    protected function checkFiles()
    {
        $files = [
            'run.sh',
            'alpine',
        ];

        foreach ($files as $file) {
            if (! file_exists("$this->path/$file")) {
                $this->error('The install failed, try again later');
                exit;
            }
        }
    }

    protected function setPermissions()
    {
        exec("cd $this->path; chmod -R 771 ./");
    }
}
