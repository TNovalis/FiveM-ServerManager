<?php

namespace App\Commands\FiveM;

use App\Commands\BaseCommand;
use Weidner\Goutte\GoutteFacade;

class UpdateCommand extends BaseCommand
{
    protected $signature = 'fivem:update {--force : Ignore warning about up-to-date server}';

    protected $description = 'Update the FiveM server files';

    protected $path;

    protected $version;

    protected $fxVersionNumber;

    /**
     * Update the FiveM server files.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->runChecks();

        $this->path = $this->setting('fivem-path');
        $this->version = $this->setting('fivem-version');

        $this->downloadFiles();

        $this->setPermissions();

        $this->setting('fivem-version', $this->fxVersionNumber);
        $this->info('FiveM has been updated!');
    }

    protected function downloadFiles()
    {
        $buildsURL = 'https://runtime.fivem.net/artifacts/fivem/build_proot_linux/master/';

        $crawler = GoutteFacade::request('GET', $buildsURL);
        $newestBuild = collect($crawler->filter('a')->each(function ($n) use ($buildsURL) {
            $link = $n->attr('href');
            if (! is_numeric(substr($link, 0, 3))) {
                return;
            }
            $version = explode('-', trim($link, '/'))[0];

            return ['version' => intval($version), 'link' => $buildsURL.$link.'fx.tar.xz'];
        }))->filter()->sortByDesc('version')->first();
        $this->fxVersionNumber = $newestBuild['version'];
        $link = $newestBuild['link'];

        if ($this->version == $this->fxVersionNumber && ! $this->option('force') && ! $this->confirm('The FiveM server is already up-to-date, continue?')) {
            exit;
        }

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
