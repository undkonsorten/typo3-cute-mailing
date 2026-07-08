<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\Services;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Routing\RouterInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\RecipientList;
use Undkonsorten\CuteMailing\Domain\Model\SendOut;
use Undkonsorten\CuteMailing\Domain\Model\SimpleRecipient;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\SendOutRepository;
use Undkonsorten\CuteMailing\Services\MailService;

#[CoversClass(MailService::class)]
final class MailServiceTest extends UnitTestCase
{
    private FrontendInterface&\PHPUnit\Framework\MockObject\MockObject $cache;
    private PersistenceManagerInterface&\PHPUnit\Framework\MockObject\MockObject $persistenceManager;
    private SendOutRepository&\PHPUnit\Framework\MockObject\MockObject $sendOutRepository;
    private NewsletterRepository&\PHPUnit\Framework\MockObject\MockObject $newsletterRepository;
    private RequestFactory&\PHPUnit\Framework\MockObject\MockObject $requestFactory;
    private UriBuilder&\PHPUnit\Framework\MockObject\MockObject $uriBuilder;
    private SiteFinder&\PHPUnit\Framework\MockObject\MockObject $siteFinder;
    private MailerInterface&\PHPUnit\Framework\MockObject\MockObject $mailer;
    private MailService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->createMock(FrontendInterface::class);
        $this->cache->method('get')->willReturn(false);
        $this->persistenceManager = $this->createMock(PersistenceManagerInterface::class);
        $this->sendOutRepository = $this->createMock(SendOutRepository::class);
        $this->newsletterRepository = $this->createMock(NewsletterRepository::class);
        $this->requestFactory = $this->createMock(RequestFactory::class);
        $this->uriBuilder = $this->createMock(UriBuilder::class);
        $this->siteFinder = $this->createMock(SiteFinder::class);
        $this->mailer = $this->createMock(MailerInterface::class);

        $this->subject = new MailService(
            $this->cache,
            $this->persistenceManager,
            $this->sendOutRepository,
            $this->newsletterRepository,
            $this->requestFactory,
            $this->uriBuilder,
            $this->siteFinder,
            $this->mailer,
        );
    }

    #[Test]
    public function sendMailThrowsWhenNewsletterNoLongerExists(): void
    {
        $this->newsletterRepository->method('findByUid')->willReturn(null);

        $mailTask = new MailTask();
        $mailTask->setNewsletter(1);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1651441455);

        $this->subject->sendMail($mailTask);
    }

    #[Test]
    public function sendMailThrowsWhenSendOutNoLongerExists(): void
    {
        $this->newsletterRepository->method('findByUid')->willReturn(new Newsletter());
        $this->sendOutRepository->method('findByUid')->willReturn(null);

        $mailTask = new MailTask();
        $mailTask->setNewsletter(1);
        $mailTask->setSendOut(1);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1697631977);

        $this->subject->sendMail($mailTask);
    }

    #[Test]
    public function sendMailThrowsWhenRecipientNoLongerExists(): void
    {
        $recipientList = $this->createMock(RecipientList::class);
        $recipientList->method('getRecipient')->willReturn(null);

        $newsletter = new Newsletter();
        $newsletter->setRecipientList($recipientList);

        $sendOut = (new SendOut())->setTest(false);

        $this->newsletterRepository->method('findByUid')->willReturn($newsletter);
        $this->sendOutRepository->method('findByUid')->willReturn($sendOut);

        $mailTask = new MailTask();
        $mailTask->setNewsletter(1);
        $mailTask->setSendOut(1);
        $mailTask->setRecipient(42);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1697631568);

        $this->subject->sendMail($mailTask);
    }

    #[Test]
    public function sendMailBuildsAndSendsHtmlEmailWithPersonalizedMarkers(): void
    {
        [$newsletter, $sendOut, $mailTask] = $this->buildScenario(MailTask::HTML, false);
        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hello ###firstName### ###lastName###</p>', '');

        $sentMessage = null;
        $this->mailer->expects(self::once())->method('send')->willReturnCallback(
            function (MailMessage $message) use (&$sentMessage): void {
                $sentMessage = $message;
            }
        );

        $this->subject->sendMail($mailTask);

        self::assertNotNull($sentMessage);
        self::assertSame('peter@example.org', $sentMessage->getTo()[0]->getAddress());
        self::assertSame('Peter Müller', $sentMessage->getTo()[0]->getName());
        self::assertSame('sender@example.org', $sentMessage->getFrom()[0]->getAddress());
        self::assertSame('Sender Name', $sentMessage->getFrom()[0]->getName());
        self::assertSame('reply@example.org', $sentMessage->getReplyTo()[0]->getAddress());
        self::assertSame('Hello Peter', $sentMessage->getSubject());
        self::assertStringContainsString('Hello Peter Müller', (string)$sentMessage->getHtmlBody());
        self::assertSame(
            base64_encode('peter@example.org'),
            $sentMessage->getHeaders()->get('X-TYPO3RCPT')->getBodyAsString(),
        );
        self::assertSame((string)$sendOut->getUid(), $sentMessage->getHeaders()->get('X-TYPO3NLUID')->getBodyAsString());
    }

    #[Test]
    public function sendMailBuildsPlaintextEmailWhenFormatIsPlaintext(): void
    {
        [, , $mailTask] = $this->buildScenario(MailTask::PLAINTEXT, false);
        $this->mockSiteRouting();
        $this->mockHttpContent('', 'Hello ###firstName###');

        $sentMessage = null;
        $this->mailer->method('send')->willReturnCallback(
            function (MailMessage $message) use (&$sentMessage): void {
                $sentMessage = $message;
            }
        );

        $this->subject->sendMail($mailTask);

        self::assertNotNull($sentMessage);
        self::assertNull($sentMessage->getHtmlBody());
        self::assertStringContainsString('Hello Peter', (string)$sentMessage->getTextBody());
    }

    #[Test]
    public function sendMailAddsListUnsubscribeHeaderWhenEnabled(): void
    {
        [$newsletter, , $mailTask] = $this->buildScenario(MailTask::HTML, false);
        $newsletter->setListunsubscribeEnable(true);
        $newsletter->setListunsubscribeEmail('unsubscribe@example.org');
        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hi</p>', '');

        $sentMessage = null;
        $this->mailer->method('send')->willReturnCallback(
            function (MailMessage $message) use (&$sentMessage): void {
                $sentMessage = $message;
            }
        );

        $this->subject->sendMail($mailTask);

        self::assertNotNull($sentMessage->getHeaders()->get('List-Unsubscribe'));
        self::assertStringContainsString('unsubscribe@example.org', $sentMessage->getHeaders()->get('List-Unsubscribe')->getBodyAsString());
    }

    #[Test]
    public function sendMailOmitsListUnsubscribeHeaderWhenDisabled(): void
    {
        [, , $mailTask] = $this->buildScenario(MailTask::HTML, false);
        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hi</p>', '');

        $sentMessage = null;
        $this->mailer->method('send')->willReturnCallback(
            function (MailMessage $message) use (&$sentMessage): void {
                $sentMessage = $message;
            }
        );

        $this->subject->sendMail($mailTask);

        self::assertNull($sentMessage->getHeaders()->get('List-Unsubscribe'));
    }

    #[Test]
    public function sendMailSetsReturnPathWhenConfigured(): void
    {
        [$newsletter, , $mailTask] = $this->buildScenario(MailTask::HTML, false);
        $newsletter->setReturnPath('bounce@example.org');
        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hi</p>', '');

        $sentMessage = null;
        $this->mailer->method('send')->willReturnCallback(
            function (MailMessage $message) use (&$sentMessage): void {
                $sentMessage = $message;
            }
        );

        $this->subject->sendMail($mailTask);

        self::assertSame('bounce@example.org', $sentMessage->getReturnPath()?->getAddress());
    }

    #[Test]
    public function sendMailOmitsReturnPathWhenNotConfigured(): void
    {
        [, , $mailTask] = $this->buildScenario(MailTask::HTML, false);
        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hi</p>', '');

        $sentMessage = null;
        $this->mailer->method('send')->willReturnCallback(
            function (MailMessage $message) use (&$sentMessage): void {
                $sentMessage = $message;
            }
        );

        $this->subject->sendMail($mailTask);

        self::assertNull($sentMessage->getReturnPath());
    }

    #[Test]
    public function sendMailIncrementsSendOutCompletedCounter(): void
    {
        [, $sendOut, $mailTask] = $this->buildScenario(MailTask::HTML, false);
        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hi</p>', '');

        self::assertSame(0, $sendOut->getCompleted());

        $this->subject->sendMail($mailTask);

        self::assertSame(1, $sendOut->getCompleted());
    }

    #[Test]
    public function sendMailMarksNewsletterSentOnceItsOnlySendOutIsComplete(): void
    {
        [$newsletter, $sendOut, $mailTask] = $this->buildScenario(MailTask::HTML, false);
        $newsletter->setStatus(Newsletter::SCHEDULED);
        $newsletter->addSendOut($sendOut);
        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hi</p>', '');

        $this->subject->sendMail($mailTask);

        self::assertSame(Newsletter::SENT, $newsletter->getStatus());
    }

    #[Test]
    public function sendMailUsesTestRecipientListWhenSendOutIsTest(): void
    {
        $recipient = new SimpleRecipient();
        $recipient->setUid(1)->setEmail('test@example.org')->setFirstName('Test')->setLastName('Recipient');

        $testRecipientList = $this->createMock(RecipientList::class);
        $testRecipientList->method('getRecipient')->willReturn($recipient);

        $realRecipientList = $this->createMock(RecipientList::class);
        $realRecipientList->expects(self::never())->method('getRecipient');

        $newsletter = new Newsletter();
        $newsletter->setNewsletterPage(1);
        $newsletter->setPageTypeHtml(10);
        $newsletter->setPageTypeText(11);
        $newsletter->setSender('sender@example.org');
        $newsletter->setSubject('Test send');
        $newsletter->setAllowedMarker('');
        $newsletter->setRecipientList($realRecipientList);
        $newsletter->setTestRecipientList($testRecipientList);

        $sendOut = (new SendOut())->setTest(true);
        $sendOut->_setProperty('uid', 7);

        $this->newsletterRepository->method('findByUid')->willReturn($newsletter);
        $this->sendOutRepository->method('findByUid')->willReturn($sendOut);

        $mailTask = new MailTask();
        $mailTask->setFormat(MailTask::HTML);
        $mailTask->setNewsletter(1);
        $mailTask->setSendOut(1);
        $mailTask->setRecipient(1);

        $this->mockSiteRouting();
        $this->mockHttpContent('<p>Hi</p>', '');

        $sentMessage = null;
        $this->mailer->method('send')->willReturnCallback(
            function (MailMessage $message) use (&$sentMessage): void {
                $sentMessage = $message;
            }
        );

        $this->subject->sendMail($mailTask);

        self::assertSame('test@example.org', $sentMessage->getTo()[0]->getAddress());
    }

    #[Test]
    public function replaceMarkerOnlyReplacesAllowedMarkers(): void
    {
        $recipient = new SimpleRecipient();
        $recipient->setFirstName('Peter');
        $recipient->setLastName('Müller');

        $content = '###firstName### ###lastName### ###email###';
        $this->invokeReplaceMarker(['firstName'], $content, $recipient);

        self::assertSame('Peter ###lastName### ###email###', $content);
    }

    #[Test]
    public function replaceMarkerHandlesUrlEncodedMarkerForm(): void
    {
        $recipient = new SimpleRecipient();
        $recipient->setFirstName('Peter');

        $content = '%23%23%23firstName%23%23%23';
        $this->invokeReplaceMarker(['firstName'], $content, $recipient);

        self::assertSame('Peter', $content);
    }

    #[Test]
    public function attachImagesReplacesImageSourceWithContentId(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn('fake-image-bytes');
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeaderLine')->with('Content-Type')->willReturn('image/png');
        $this->requestFactory->method('request')->willReturn($response);

        $this->setProtectedProperty('requestOptions', []);
        $this->setProtectedProperty('email', new MailMessage());

        $method = new \ReflectionMethod($this->subject, 'attachImages');
        $method->setAccessible(true);
        $html = $method->invoke($this->subject, '<img src="https://example.org/logo.png" alt="Logo">');

        self::assertMatchesRegularExpression('/src="cid:[^"]+"/', $html);
        self::assertStringNotContainsString('https://example.org/logo.png', $html);
    }

    /**
     * @return array{0: Newsletter, 1: SendOut, 2: MailTask}
     */
    private function buildScenario(string $format, bool $test): array
    {
        $recipient = new SimpleRecipient();
        $recipient->setUid(0)->setEmail('peter@example.org')->setFirstName('Peter')->setLastName('Müller');

        $recipientList = $this->createMock(RecipientList::class);
        $recipientList->method('getRecipient')->willReturn($recipient);

        $newsletter = new Newsletter();
        $newsletter->setNewsletterPage(12);
        $newsletter->setPageTypeHtml(10);
        $newsletter->setPageTypeText(11);
        $newsletter->setLanguage(0);
        $newsletter->setSender('sender@example.org');
        $newsletter->setSenderName('Sender Name');
        $newsletter->setReplyTo('reply@example.org');
        $newsletter->setReplyToName('Reply Name');
        $newsletter->setSubject('Hello Peter');
        $newsletter->setAllowedMarker('firstName,lastName');
        $newsletter->setRecipientList($recipientList);
        $newsletter->setTestRecipientList($recipientList);

        $newsletter->_setProperty('uid', 1);

        $sendOut = (new SendOut())->setTest($test);
        $sendOut->setTotal(1);
        $sendOut->_setProperty('uid', 7);

        $this->newsletterRepository->method('findByUid')->willReturn($newsletter);
        $this->sendOutRepository->method('findByUid')->willReturn($sendOut);

        $mailTask = new MailTask();
        $mailTask->setFormat($format);
        $mailTask->setNewsletter(1);
        $mailTask->setSendOut(1);
        $mailTask->setRecipient(0);

        return [$newsletter, $sendOut, $mailTask];
    }

    private function mockSiteRouting(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $router->method('generateUri')->willReturnCallback(
            fn($pageId, array $parameters = []): Uri => new Uri('https://example.org/newsletter?type=' . ($parameters['type'] ?? ''))
        );

        $site = $this->createMock(Site::class);
        $site->method('getRouter')->willReturn($router);

        $this->siteFinder->method('getSiteByPageId')->willReturn($site);
    }

    private function mockHttpContent(string $htmlContent, string $textContent): void
    {
        $this->requestFactory->method('request')->willReturnCallback(
            function (string $uri) use ($htmlContent, $textContent): ResponseInterface {
                $body = $this->createMock(StreamInterface::class);
                $body->method('getContents')->willReturn(str_contains($uri, 'type=10') ? $htmlContent : $textContent);

                $response = $this->createMock(ResponseInterface::class);
                $response->method('getBody')->willReturn($body);
                return $response;
            }
        );
    }

    private function invokeReplaceMarker(array $allowedMarker, string &$content, SimpleRecipient $recipient): void
    {
        $method = new \ReflectionMethod($this->subject, 'replaceMarker');
        $method->setAccessible(true);
        $method->invokeArgs($this->subject, [$allowedMarker, &$content, $recipient]);
    }

    private function setProtectedProperty(string $name, mixed $value): void
    {
        $property = new \ReflectionProperty($this->subject, $name);
        $property->setAccessible(true);
        $property->setValue($this->subject, $value);
    }
}
