<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Functional\Controller;

use Codappix\Typo3PhpDatasets\TestingFramework as PhpDatasets;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Http\Application;
use TYPO3\CMS\Backend\Routing\Route;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Controller\NewsletterController;

#[CoversClass(NewsletterController::class)]
final class NewsletterControllerTest extends FunctionalTestCase
{
    use PhpDatasets;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'undkonsorten/typo3-cute-mailing',
            'undkonsorten/taskqueue'
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-fluid-styled-content',
        ];

        parent::setUp();
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/NewsletterController/pages.php');
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/NewsletterController/be_users.php');
        $backendUser = $this->setUpBackendUser(1);
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->createFromUserPreferences($backendUser);
    }

    #[Test]
    public function newSelfAssertionActionShowsForm(): void
    {

        #$request = (new ServerRequest('https://example.com/typo3/module/page'))
        #    ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
        #    ->withAttribute('route', new Route('/module/page', ['packageName' => 'undkonsorten/cute-mailing', '_identifier' => 'cutemailing']));
        $request = (new ServerRequest('https://typo3-testing.local/typo3/main'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withQueryParams(['redirect' => 'site_configuration'])
            ->withAttribute('route', new Route('/main', ['packageName' => 'typo3/cms-backend', '_identifier' => 'main']));
        $request->

        $GLOBALS['TYPO3_REQUEST'] = $request;

        /** @var Application $subject */
        $subject = $this->get(Application::class);
        $response = $subject->handle($request);
        GeneralUtility::makeInstance(Context::class)->getAspect('backend.user');

        self::assertInstanceOf(AfterBackendPageRenderEvent::class, $state['after-backend-page-render-listener']);
    }



}
