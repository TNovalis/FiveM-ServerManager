<?php

namespace App\Commands\Config;

use App\Commands\BaseCommand;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

class MenuCommand extends BaseCommand
{
    protected $signature = 'config:menu';

    protected $description = 'Opens server manager config menu';

    public function handle()
    {
        $_this = $this;

        $options = [
            'backups'   => [
                'enable'      => function (CliMenu $menu) use ($_this) {
                    $_this->setting('backups.enabled', true);
                    $menu->flash('Backups Enabled!')->display();
                },
                'disable'     => function (CliMenu $menu) use ($_this) {
                    $_this->setting('backups.enabled', false);
                    $menu->flash('Backups Disabled!')->display();
                },
                'max'         => function (CliMenu $menu) use ($_this) {
                    $max = intval($menu->askNumber()->setPromptText('Max Backups')
                                       ->setPlaceholderText($_this->setting('backups.max'))
                                       ->setValidationFailedText('Must be a number!')->ask()->fetch());

                    $_this->setting('backups.max', $max);
                    $menu->flash('Max backups set to '.$max)->display();
                },
                'setInterval' => function (CliMenu $menu) use ($_this) {
                    $every = intval($menu->askNumber()->setPromptText('Backup Interval (in minutes)')
                                         ->setPlaceholderText($_this->setting('backups.every'))
                                         ->setValidationFailedText('Must be a number!')->ask()->fetch());

                    $_this->setting('backups.every', $every);
                    $menu->flash('Backups set to every '.$every.' '.str_plural('minute', $every))->display();
                },
            ],
            'restarts'  => [
                'enable'  => function (CliMenu $menu) use ($_this) {
                    $_this->setting('restarts.enabled', true);
                    $menu->flash('Restarts Enabled!')->display();
                },
                'disable' => function (CliMenu $menu) use ($_this) {
                    $_this->setting('restarts.enabled', false);
                    $menu->flash('Restarts Disabled!')->display();
                },
                'setTime' => function (CliMenu $menu) use ($_this) {
                    $time = $menu->askText()->setPromptText('Restart Time (local to machine, 24-hour)')
                                 ->setPlaceholderText($_this->setting('restarts.time'))
                                 ->setValidationFailedText('You must enter a time')->ask();
                    $_this->setting('restarts.time', $time->fetch());
                    $menu->flash('Servers will restart everyday at '.$time->fetch())->display();
                },
            ],
            'crash_fix' => [
                'enable'  => function (CliMenu $menu) use ($_this) {
                    $_this->setting('crash_fix.enabled', true);
                    $menu->flash('Crash Fix Enabled!')->display();
                },
                'disable' => function (CliMenu $menu) use ($_this) {
                    $_this->setting('crash_fix.enabled', false);
                    $menu->flash('Crash Fix Disabled!')->display();
                },
            ],
        ];

        $menu = [
            'backups'   => function (CliMenuBuilder $b) use ($_this, $options) {
                $b->setTitle('FSM > Config > Backups');
                $b->addStaticItem(($_this->setting('backups.enabled') ? '[ENABLED]' : '[DISABLED]').' - Automatic backups every '.$_this->setting('backups.every').' minutes. Max: '.$_this->setting('backups.max'));
                $b->addLineBreak('-');
                $b->addItem('Enable', $options['backups']['enable']);
                $b->addItem('Disable', $options['backups']['disable']);
                $b->addItem('Set Max', $options['backups']['max']);
                $b->addItem('Set Interval', $options['backups']['setInterval']);
                $b->addLineBreak('-');
            },
            'restarts'  => function (CliMenuBuilder $b) use ($_this, $options) {
                $b->setTitle('FSM > Config > Restarts');
                $b->addStaticItem(($_this->setting('restarts.enabled') ? '[ENABLED]' : '[DISABLED]').' - Automatic restarts at '.$_this->setting('restarts.time'));
                $b->addStaticItem('Note: Time is in relation to UTC');
                $b->addLineBreak('-');
                $b->addItem('Enable', $options['restarts']['enable']);
                $b->addItem('Disable', $options['restarts']['disable']);
                $b->addItem('Set Time', $options['restarts']['setTime']);
                $b->addLineBreak('-');
            },
            'crash_fix' => function (CliMenuBuilder $b) use ($_this, $options) {
                $b->setTitle('FSM > Config > Crash Fix');
                $b->addStaticItem(($_this->setting('crash_fix.enabled') ? '[ENABLED]' : '[DISABLED]').' - Automatically restart crashed servers');
                $b->addStaticItem('Note: This also affects commands that will ask to restart a server');
                $b->addLineBreak('-');
                $b->addItem('Enable', $options['crash_fix']['enable']);
                $b->addItem('Disable', $options['crash_fix']['disable']);
                $b->addLineBreak('-');
            },
            'license'   => function (CliMenu $menu) use ($_this) {
                $license = $menu->askText()->setPromptText('License')->setPlaceholderText($_this->setting('license') ?: '')
                                ->ask();
                $_this->setting('license', $license->fetch());
                $menu->flash('License set to '.$license->fetch())->display();
            },
        ];

        $this->menu('FSM > Config')
             ->addStaticItem('Due to limitations, the display will not update until the menu is closed.')
             ->addLineBreak('-')->addSubMenu('Backups', $menu['backups'])->addSubMenu('Restarts', $menu['restarts'])
             ->addSubMenu('Crash Fix', $menu['crash_fix'])->addItem('Set License', $menu['license'])
             ->setUnselectedMarker('□ ')->setSelectedMarker('■ ')->addLineBreak('-')->open();
    }
}
