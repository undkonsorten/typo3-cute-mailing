<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\ViewHelpers\Be\Security;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use Undkonsorten\CuteMailing\ViewHelpers\Be\Security\IfIsAdminViewHelper;

#[CoversClass(IfIsAdminViewHelper::class)]
final class IfIsAdminViewHelperTest extends UnitTestCase
{
    private mixed $originalBeUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalBeUser = $GLOBALS['BE_USER'] ?? null;
    }

    protected function tearDown(): void
    {
        $GLOBALS['BE_USER'] = $this->originalBeUser;
        parent::tearDown();
    }

    #[Test]
    public function verdictReturnsTrueForAdminUser(): void
    {
        $beUser = $this->createMock(BackendUserAuthentication::class);
        $beUser->method('isAdmin')->willReturn(true);
        $GLOBALS['BE_USER'] = $beUser;

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        self::assertTrue(IfIsAdminViewHelper::verdict([], $renderingContext));
    }

    #[Test]
    public function verdictReturnsFalseForNonAdminUser(): void
    {
        $beUser = $this->createMock(BackendUserAuthentication::class);
        $beUser->method('isAdmin')->willReturn(false);
        $GLOBALS['BE_USER'] = $beUser;

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        self::assertFalse(IfIsAdminViewHelper::verdict([], $renderingContext));
    }

    #[Test]
    public function verdictReturnsFalseWhenNoBackendUserIsLoggedIn(): void
    {
        $GLOBALS['BE_USER'] = null;

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        self::assertFalse(IfIsAdminViewHelper::verdict([], $renderingContext));
    }
}
