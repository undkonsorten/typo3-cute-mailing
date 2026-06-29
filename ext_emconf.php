<?php

declare(strict_types=1);

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Cute mailing',
    'description' => 'A TYPO3 extension that makes mailing and newsletter sending cute. Send TYPO3 pages as emails (newsletter) to many recipients. Can be used as replacement for direct_mail.',
    'category' => 'module',
    'author' => 'Eike Starkmann',
    'author_email' => 'starkmann@undkonsorten.com',
    'state' => 'stable',
    'version' => '4.1.4',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
            'taskqueue' => '8.0.0-9.99.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
