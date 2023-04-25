<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;

class SendOut extends AbstractEntity
{

    /**
     * @var Newsletter
     * @Lazy
     */
    protected $newsletter;

    /**
     * @var bool
     */
    protected $test;

    /**
     * @var ObjectStorage<MailTask>
     * @Cascade("remove")
     * @Lazy
     */
    protected $mailTasks;

    protected int $total = 0;

    protected int $completed = 0;

    public function __construct()
    {
        $this->mailTasks = new ObjectStorage();
    }

    public function setNewsletter(Newsletter $newsletter): self
    {
        $this->newsletter = $newsletter;
        return $this;
    }

    public function getNewsletter(): Newsletter
    {
        return $this->newsletter;
    }

    public function setMailTasks(ObjectStorage $mailTasks): self
    {
        $this->mailTasks = $mailTasks;
        return $this;
    }

    public function getMailTasks(): ObjectStorage
    {
        return $this->mailTasks;
    }

    public function addMailTask(MailTask $mailTask): self
    {
        $this->mailTasks->attach($mailTask);
        return $this;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getCompletedMailTasks(): array
    {
        return array_filter($this->getMailTasks()->toArray(), static function (MailTask $task): bool {
            return $task->getStatus() === TaskInterface::FAILED || $task->getStatus() === TaskInterface::FINISHED;
        });
    }

    public function getProgress(): float
    {
        if ($this->total === 0) {
            return 0.0;
        }
        return (float)$this->completed / $this->total;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function isComplete(): bool
    {
        return $this->getProgress() === 1.0;
    }

    public function hasStarted(): bool
    {
        return $this->getProgress() > 0;
    }

    public function isTest(): bool
    {
        return $this->test;
    }

    public function setTest(bool $test): self
    {
        $this->test = $test;
        return $this;
    }

    public function getCompleted(): int
    {
        return $this->completed;
    }

    public function setCompleted(int $completed): self
    {
        $this->completed = $completed;
        return $this;
    }

    public function incrementCompleted(): self
    {
        $this->completed++;
        return $this;
    }

    public function isScheduled(): bool
    {
        return $this->getProgress() === 0.0;
    }

    public function isRunning(): bool
    {
        return $this->hasStarted() && !$this->isComplete();
    }

}
