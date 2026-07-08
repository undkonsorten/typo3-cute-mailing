<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Functional\Domain\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Domain\Model\LineSeparatedRecipientList;
use Undkonsorten\CuteMailing\Domain\Repository\RecipientListRepository;

#[CoversClass(RecipientListRepository::class)]
final class RecipientListRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/typo3-cute-mailing',
    ];

    private RecipientListRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->get(RecipientListRepository::class);
    }

    #[Test]
    public function findByRootlineReturnsOnlyRecipientListsOnPagesFromTheRootline(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByRootline/RecipientListsOnDifferentPages.csv');

        $result = $this->subject->findByRootline([['uid' => 10], ['uid' => 20]]);

        self::assertCount(2, $result);
        $names = array_map(static fn(LineSeparatedRecipientList $list): string => $list->getName(), iterator_to_array($result));
        self::assertContains('List A', $names);
        self::assertContains('List B', $names);
        self::assertNotContains('List C', $names);
    }

    #[Test]
    public function findByRootlineReturnsEmptyResultForUnrelatedPages(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/findByRootline/RecipientListsOnDifferentPages.csv');

        $result = $this->subject->findByRootline([['uid' => 999]]);

        self::assertCount(0, $result);
    }
}
