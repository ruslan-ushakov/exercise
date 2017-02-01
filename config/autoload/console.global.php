<?php

return [
    'dependencies' => [
        'invokables' => [
            App\Command\UserTextUtilCommand::class,
        ],
        'factories' => [],
    ],
    'console' => [
        'commands' => [
            App\Command\UserTextUtilCommand::class,
        ],
    ],
];