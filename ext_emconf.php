<?php

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Cute Mailing',
    'description' => 'Send TYPO3 pages as emails (newsletter) to many recipients. Can be used as replacement for direct_mail.',
    'category' => 'plugin',
    'author' => 'undkonsorten',
    'author_email' => 'kontakt@undkonsorten.com',
    'state' => 'stable',
    'version' => '3.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'taskqueue' => '8.0.0-8.99.99',
        ],
    ],
];
