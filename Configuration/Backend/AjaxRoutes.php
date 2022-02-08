<?php

use Undkonsorten\CuteMailing\Controller\NewsletterController;

return [
    '/luxletter/wizardUserPreview' => [
        'path' => '/luxletter/wizardUserPreview',
        'target' => NewsletterController::class . '::wizardUserPreviewAjax',
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
