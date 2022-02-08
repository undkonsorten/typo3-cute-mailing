<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}
call_user_func(
    function () {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:cute_mailing/Configuration/TypoScript/constants.typoscript"');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:cute_mailing/Configuration/TypoScript/setup.typoscript"');


    }
);

