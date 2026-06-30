<?php

declare(strict_types=1);

use Undkonsorten\CuteMailing\Controller\NewsletterController;

return [
    '/cutemailing/wizardUserPreview' => [
        'path' => '/cutemailing/wizardUserPreview',
        'target' => implode('::', [NewsletterController::class, 'wizardUserPreviewAjax']),
    ],
];
