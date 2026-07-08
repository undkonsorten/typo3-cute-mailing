<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\Domain\Model;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\CuteMailing\Domain\Model\LineSeparatedRecipientList;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\SendOut;

#[CoversClass(Newsletter::class)]
final class NewsletterTest extends UnitTestCase
{
    private Newsletter $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Newsletter();
    }

    #[Test]
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame('', $this->subject->getTitle());
    }

    #[Test]
    public function setTitleSetsTitle(): void
    {
        $this->subject->setTitle('My Newsletter');
        self::assertSame('My Newsletter', $this->subject->getTitle());
    }

    #[Test]
    public function setAndGetSubjectRoundTrips(): void
    {
        $this->subject->setSubject('Hello there');
        self::assertSame('Hello there', $this->subject->getSubject());
    }

    #[Test]
    public function setAndGetSenderRoundTrips(): void
    {
        $this->subject->setSender('sender@example.org');
        $this->subject->setSenderName('Sender Name');
        self::assertSame('sender@example.org', $this->subject->getSender());
        self::assertSame('Sender Name', $this->subject->getSenderName());
    }

    #[Test]
    public function setAndGetReplyToRoundTrips(): void
    {
        $this->subject->setReplyTo('reply@example.org');
        $this->subject->setReplyToName('Reply Name');
        self::assertSame('reply@example.org', $this->subject->getReplyTo());
        self::assertSame('Reply Name', $this->subject->getReplyToName());
    }

    #[Test]
    public function setAndGetNewsletterPageRoundTrips(): void
    {
        $this->subject->setNewsletterPage(42);
        self::assertSame(42, $this->subject->getNewsletterPage());
    }

    #[Test]
    public function setAndGetNewsletterPageUrlRoundTrips(): void
    {
        $this->subject->setNewsletterPageUrl('https://example.org/newsletter');
        self::assertSame('https://example.org/newsletter', $this->subject->getNewsletterPageUrl());
    }

    #[Test]
    public function setAndGetPageTypeHtmlRoundTrips(): void
    {
        self::assertNull($this->subject->getPageTypeHtml());
        $this->subject->setPageTypeHtml(10);
        self::assertSame(10, $this->subject->getPageTypeHtml());
    }

    #[Test]
    public function setAndGetPageTypeTextRoundTrips(): void
    {
        self::assertNull($this->subject->getPageTypeText());
        $this->subject->setPageTypeText(11);
        self::assertSame(11, $this->subject->getPageTypeText());
    }

    #[Test]
    public function setAndGetAllowedMarkerRoundTrips(): void
    {
        $this->subject->setAllowedMarker('firstName,lastName');
        self::assertSame('firstName,lastName', $this->subject->getAllowedMarker());
    }

    #[Test]
    public function setAndGetReturnPathRoundTrips(): void
    {
        self::assertNull($this->subject->getReturnPath());
        $result = $this->subject->setReturnPath('bounce@example.org');
        self::assertSame($this->subject, $result);
        self::assertSame('bounce@example.org', $this->subject->getReturnPath());
    }

    #[Test]
    public function setAndGetLanguageRoundTrips(): void
    {
        self::assertSame(0, $this->subject->getLanguage());
        $result = $this->subject->setLanguage(2);
        self::assertSame($this->subject, $result);
        self::assertSame(2, $this->subject->getLanguage());
    }

    #[Test]
    public function setAndGetBasicAuthCredentialsRoundTrip(): void
    {
        $this->subject->setBasicAuthUser('user');
        $this->subject->setBasicAuthPassword('secret');
        self::assertSame('user', $this->subject->getBasicAuthUser());
        self::assertSame('secret', $this->subject->getBasicAuthPassword());
    }

    #[Test]
    public function setAndGetListunsubscribeSettingsRoundTrip(): void
    {
        self::assertNull($this->subject->getListunsubscribeEnable());
        self::assertNull($this->subject->getListunsubscribeEmail());
        $this->subject->setListunsubscribeEnable(true);
        $this->subject->setListunsubscribeEmail('unsubscribe@example.org');
        self::assertTrue($this->subject->getListunsubscribeEnable());
        self::assertSame('unsubscribe@example.org', $this->subject->getListunsubscribeEmail());
    }

    #[Test]
    public function setAndGetRecipientListRoundTrips(): void
    {
        $recipientList = new LineSeparatedRecipientList();
        $this->subject->setRecipientList($recipientList);
        self::assertSame($recipientList, $this->subject->getRecipientList());
    }

    #[Test]
    public function getRecipientListThrowsWhenNeverSet(): void
    {
        // The getter is declared to return a non-nullable RecipientList, but the
        // property defaults to null, so PHP throws a TypeError before any of our
        // code runs. This documents that behaviour rather than a chosen design.
        $this->expectException(\TypeError::class);
        $this->subject->getRecipientList();
    }

    #[Test]
    public function testRecipientListInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getTestRecipientList());
    }

    #[Test]
    public function setAndGetTestRecipientListRoundTrips(): void
    {
        $recipientList = new LineSeparatedRecipientList();
        $this->subject->setTestRecipientList($recipientList);
        self::assertSame($recipientList, $this->subject->getTestRecipientList());
    }

    #[Test]
    public function getSendingTimeReturnsCurrentTimeWhenNeverSet(): void
    {
        $before = new DateTime();
        $sendingTime = $this->subject->getSendingTime();
        $after = new DateTime();

        self::assertGreaterThanOrEqual($before->getTimestamp(), $sendingTime->getTimestamp());
        self::assertLessThanOrEqual($after->getTimestamp(), $sendingTime->getTimestamp());
    }

    #[Test]
    public function setAndGetSendingTimeRoundTrips(): void
    {
        $dateTime = new DateTime('2030-01-01 10:00:00');
        $this->subject->setSendingTime($dateTime);
        self::assertSame($dateTime, $this->subject->getSendingTime());
    }

    #[Test]
    public function statusInitiallyIsCreated(): void
    {
        self::assertSame(Newsletter::CREATED, $this->subject->getStatus());
    }

    #[Test]
    public function enableSetsStatusToScheduled(): void
    {
        $this->subject->enable();
        self::assertSame(Newsletter::SCHEDULED, $this->subject->getStatus());
        self::assertTrue($this->subject->isScheduled());
    }

    #[Test]
    public function markSentSetsStatusToSentAndReturnsSelf(): void
    {
        $result = $this->subject->markSent();
        self::assertSame($this->subject, $result);
        self::assertSame(Newsletter::SENT, $this->subject->getStatus());
        self::assertTrue($this->subject->isSent());
    }

    #[Test]
    public function isScheduledIsFalseForOtherStatuses(): void
    {
        $this->subject->setStatus(Newsletter::CREATED);
        self::assertFalse($this->subject->isScheduled());
    }

    #[Test]
    public function isSentIsFalseForOtherStatuses(): void
    {
        $this->subject->setStatus(Newsletter::SCHEDULED);
        self::assertFalse($this->subject->isSent());
    }

    #[Test]
    public function getSendOutsInitiallyReturnsEmptyObjectStorage(): void
    {
        self::assertCount(0, $this->subject->getSendOuts());
    }

    #[Test]
    public function addSendOutAddsToSendOuts(): void
    {
        $sendOut = new SendOut();
        $result = $this->subject->addSendOut($sendOut);

        self::assertSame($this->subject, $result);
        self::assertCount(1, $this->subject->getSendOuts());
        self::assertTrue($this->subject->getSendOuts()->contains($sendOut));
    }

    #[Test]
    public function getLatestSendOutReturnsNullWhenEmpty(): void
    {
        self::assertNull($this->subject->getLatestSendOut());
    }

    #[Test]
    public function getLatestSendOutReturnsLastAddedSendOut(): void
    {
        $first = (new SendOut())->setTest(false);
        $second = (new SendOut())->setTest(false);
        $this->subject->addSendOut($first);
        $this->subject->addSendOut($second);

        self::assertSame($second, $this->subject->getLatestSendOut());
    }

    #[Test]
    public function getLatestNonTestSendoutReturnsNullWhenEmpty(): void
    {
        self::assertNull($this->subject->getLatestNonTestSendout());
    }

    #[Test]
    public function getLatestNonTestSendoutIgnoresTestSendouts(): void
    {
        $real = (new SendOut())->setTest(false);
        $test = (new SendOut())->setTest(true);
        $this->subject->addSendOut($real);
        $this->subject->addSendOut($test);

        self::assertSame($real, $this->subject->getLatestNonTestSendout());
    }

    #[Test]
    public function getProgressReturnsZeroWithoutSendOuts(): void
    {
        self::assertSame(0.0, $this->subject->getProgress());
    }

    #[Test]
    public function getProgressDelegatesToLatestNonTestSendout(): void
    {
        $sendOut = (new SendOut())->setTest(false);
        $sendOut->setTotal(4);
        $sendOut->incrementCompleted();
        $this->subject->addSendOut($sendOut);

        self::assertSame(0.25, $this->subject->getProgress());
    }

    #[Test]
    public function isCompleteIsFalseWithoutSendOuts(): void
    {
        self::assertFalse($this->subject->isComplete());
    }

    #[Test]
    public function isCompleteIsTrueWhenLatestNonTestSendoutIsComplete(): void
    {
        $sendOut = (new SendOut())->setTest(false);
        $sendOut->setTotal(1);
        $sendOut->incrementCompleted();
        $this->subject->addSendOut($sendOut);

        self::assertTrue($this->subject->isComplete());
    }

    #[Test]
    public function updateStatusMarksSentWhenScheduledAndComplete(): void
    {
        $sendOut = (new SendOut())->setTest(false);
        $sendOut->setTotal(1);
        $sendOut->incrementCompleted();
        $this->subject->addSendOut($sendOut);
        $this->subject->setStatus(Newsletter::SCHEDULED);

        $result = $this->subject->updateStatus();

        self::assertSame($this->subject, $result);
        self::assertSame(Newsletter::SENT, $this->subject->getStatus());
    }

    #[Test]
    public function updateStatusDoesNothingWhenNotScheduled(): void
    {
        $sendOut = (new SendOut())->setTest(false);
        $sendOut->setTotal(1);
        $sendOut->incrementCompleted();
        $this->subject->addSendOut($sendOut);
        $this->subject->setStatus(Newsletter::CREATED);

        $this->subject->updateStatus();

        self::assertSame(Newsletter::CREATED, $this->subject->getStatus());
    }

    #[Test]
    public function updateStatusDoesNothingWhenScheduledButIncomplete(): void
    {
        $sendOut = (new SendOut())->setTest(false);
        $sendOut->setTotal(4);
        $sendOut->incrementCompleted();
        $this->subject->addSendOut($sendOut);
        $this->subject->setStatus(Newsletter::SCHEDULED);

        $this->subject->updateStatus();

        self::assertSame(Newsletter::SCHEDULED, $this->subject->getStatus());
    }
}
