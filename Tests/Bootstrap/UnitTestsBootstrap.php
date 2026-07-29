<?php

(static function (): void {
    $envVendorDir = getenv('VENDOR_DIR');
    $candidates = array_filter([
        $envVendorDir !== false ? $envVendorDir . '/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php' : null,
        // standalone dev environment (see Build/Scripts/runTests.sh)
        __DIR__ . '/../../.Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php',
        // extension installed inside a bigger project's vendor/ directory
        __DIR__ . '/../../../../typo3/testing-framework/Resources/Core/Build/UnitTestsBootstrap.php',
    ]);

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            require_once $candidate;
            return;
        }
    }

    throw new RuntimeException(
        'Could not locate typo3/testing-framework UnitTestsBootstrap.php. Tried: ' . implode(', ', $candidates),
        1751980800
    );
})();
