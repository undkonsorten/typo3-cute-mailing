<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Functional\Domain\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Domain\Model\SendOut;
use Undkonsorten\CuteMailing\Domain\Repository\SendOutRepository;

#[CoversClass(SendOutRepository::class)]
final class SendOutRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/typo3-cute-mailing',
    ];

    private SendOutRepository $subject;

    private PersistenceManagerInterface $persistenceManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->get(SendOutRepository::class);
        $this->persistenceManager = $this->get(PersistenceManagerInterface::class);
    }

    #[Test]
    public function isRepository(): void
    {
        self::assertInstanceOf(Repository::class, $this->subject);
    }

    #[Test]
    public function addAndPersistAllCreatesNewRecord(): void
    {
        $sendOut = new SendOut();
        $sendOut->setTest(false);
        $sendOut->setTotal(5);
        $sendOut->setCompleted(2);

        $this->subject->add($sendOut);
        $this->persistenceManager->persistAll();

        self::assertIsInt($sendOut->getUid());

        $persisted = $this->subject->findByUid($sendOut->getUid());
        self::assertInstanceOf(SendOut::class, $persisted);
        self::assertSame(5, $persisted->getTotal());
        self::assertSame(2, $persisted->getCompleted());
    }

    #[Test]
    public function findByUidForInexistentRecordReturnsNull(): void
    {
        self::assertNull($this->subject->findByUid(1));
    }
}
