<?php

return [
    'cutemailing' => [
        'parent' => 'web',
        'position' => ['after' => 'recycler'],
        'access' => 'user',
        'icon' => 'EXT:cute_mailing/Resources/Public/Icons/CuteMailing.svg',
        'workspaces' => 'live',
        'path' => '/module/page/cute-mailing',
        'labels' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_mod_newsletter.xlf',
        'extensionName' => 'CuteMailing',
        'controllerActions' => [
            \Undkonsorten\CuteMailing\Controller\NewsletterController::class => [
                'list','new','edit','create','enable','delete','receiver','sendTestMail','update',
            ],
        ],
    ]
];
