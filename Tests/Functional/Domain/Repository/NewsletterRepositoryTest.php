<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Functional\Domain\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;

#[CoversClass(NewsletterRepository::class)]
final class NewsletterRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/typo3-cute-mailing',
    ];

    private NewsletterRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->get(NewsletterRepository::class);
    }

    #[Test]
    public function findByRootlineReturnsOnlyNewslettersOnPagesFromTheRootline(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByRootline/NewslettersOnDifferentPages.csv');

        $result = $this->subject->findByRootline([['uid' => 10], ['uid' => 20]]);

        self::assertInstanceOf(QueryResultInterface::class, $result);
        self::assertCount(2, $result);
        $uids = array_map(static fn(Newsletter $newsletter): int => $newsletter->getUid(), iterator_to_array($result));
        self::assertNotContains(3, $uids);
    }

    #[Test]
    public function findByRootlineOrdersByCrdateDescending(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByRootline/NewslettersOnDifferentPages.csv');

        $result = $this->subject->findByRootline([['uid' => 10], ['uid' => 20]]);

        $items = iterator_to_array($result);
        self::assertSame(2, $items[0]->getUid());
        self::assertSame(1, $items[1]->getUid());
    }

    #[Test]
    public function findByRootlineReturnsEmptyResultForUnrelatedPages(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByRootline/NewslettersOnDifferentPages.csv');

        $result = $this->subject->findByRootline([['uid' => 999]]);

        self::assertCount(0, $result);
    }
}
