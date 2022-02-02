<?php

use Composer\Factory;
use Composer\IO\NullIO;
(function () {
    $vendorDir = getenv('VENDOR_DIR') ?: (Factory::create(new NullIO()))->getConfig()->get('vendor-dir');
    /** @noinspection PhpIncludeInspection */
    require_once $vendorDir . '/typo3/testing-framework/Resources/Core/Build/FunctionalTestsBootstrap.php';
})();
