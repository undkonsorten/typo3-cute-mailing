<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Undkonsorten\CuteMailing\Controller\NewsletterController;

if (!defined('TYPO3')) {
    die('Access denied.');
}
(function () {

    (function ($extKey = 'cuteMailing') {
        $cuteMailingDoktype = 116;

        // Add new page type:
        GeneralUtility::makeInstance(\TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry::class)->add($cuteMailingDoktype, [
            'type' => 'web',
            'allowedTables' => '*',
        ]);

    })();
})();
