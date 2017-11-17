<?php

return [
    'default' => env('FILESYSTEM_DRIVER', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => $_SERVER['HOME'].'/.fsm',
        ],
    ],
];
