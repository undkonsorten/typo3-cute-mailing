<?php

use Undkonsorten\CuteMailing\Controller\NewsletterController;

return [
    '/cutemailing/wizardUserPreview' => [
        'path' => '/cutemailing/wizardUserPreview',
        'target' => implode('::', [NewsletterController::class, 'wizardUserPreviewAjax']),
    ],
    '/luxletter/receiverdetail' => [
        'path' => '/luxletter/receiverdetail',
        'target' => NewsletterController::class . '::receiverDetailAjax',
    ]
];
