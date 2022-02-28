<?php

use Undkonsorten\CuteMailing\Controller\NewsletterController;

return [
    '/cutemailing/wizardUserPreview' => [
        'path' => '/cutemailing/wizardUserPreview',
        'target' => implode('::', [NewsletterController::class, 'wizardUserPreviewAjax']),
    ],
    '/luxletter/testMail' => [
        'path' => '/luxletter/testMail',
        'target' => NewsletterController::class . '::testMailAjax',
    ],
    '/luxletter/receiverdetail' => [
        'path' => '/luxletter/receiverdetail',
        'target' => NewsletterController::class . '::receiverDetailAjax',
    ]
];
