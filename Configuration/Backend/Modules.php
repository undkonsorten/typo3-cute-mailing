<?php

declare(strict_types=1);

use Undkonsorten\CuteMailing\Controller\NewsletterController;

return [
    'cutemailing' => [
        'parent' => 'web',
        'position' => ['after' => 'recycler'],
        'access' => 'user',
        'iconIdentifier' => 'apps-cute-mailing',
        'workspaces' => 'live',
        'path' => '/module/page/cute-mailing',
        'labels' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_mod_newsletter.xlf',
        'extensionName' => 'CuteMailing',
        'controllerActions' => [
            NewsletterController::class => [
                'list', 'new', 'edit', 'create', 'enable', 'delete', 'receiver', 'sendTestMail', 'update',
            ],
        ],
    ],
];
