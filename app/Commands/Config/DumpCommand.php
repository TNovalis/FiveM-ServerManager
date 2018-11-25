<?php

namespace App\Commands\Config;

use App\Commands\BaseCommand;
use Symfony\Component\Console\Helper\TableSeparator;

class DumpCommand extends BaseCommand
{
    protected $signature = 'config:dump';

    protected $description = 'Gets server manager config';

    public function handle()
    {
        $settings = $this->getConfig('settings');

        $options = [
            ['FiveM Path', $this->setting('fivem-path') ?: '<error>Not Installed</error>'],
            new TableSeparator(),
            ['FiveM Version', $this->setting('fivem-version') ?: '<error>Not Installed</error>'],
            new TableSeparator(),
            ['Server Path', $this->setting('server-path') ?: '<comment>Not Set</comment>'],
            new TableSeparator(),
            ['Backups', $this->setting('backups.enabled') ? '<info>Enabled</info>' : '<comment>Disabled</comment>'],
            ['', '<comment>Every '.$this->setting('backups.every').' minutes</comment>'],
            ['', '<comment>Last: '.$this->setting('backups.last').'</comment>'],
            new TableSeparator(),
            ['Restarts', $this->setting('restarts.enabled') ? '<info>Enabled</info>' : '<comment>Disabled</comment>'],
            ['', '<comment>At '.$this->setting('restarts.time').'</comment>'],
            new TableSeparator(),
            ['Crash Fix', $this->setting('crash_fix.enabled') ? '<info>Enabled</info>' : '<comment>Disabled</comment>'],
            new TableSeparator(),
            ['License', $this->setting('license') ?: '<comment>Not Set</comment>'],
        ];

        $this->table(['Option', 'Value'], $options);
    }
}
