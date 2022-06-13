<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\SendOutRepository;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;
use TYPO3\CMS\Extbase\Annotation as Extbase;

class NewsletterTask extends Task
{

    /**
     * @var NewsletterRepository
     */
    protected $newsletterRepository;

    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var Newsletter
     */
    protected $newsletter;

    protected SendOutRepository $sendOutRepository;

    public function injectSendOutRepository(SendOutRepository $sendOutRepository): void
    {
        $this->sendOutRepository = $sendOutRepository;
    }

    public function injectNewsletterRepository(NewsletterRepository $newsletterRepository)
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    public function injectTaskRepository(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        if (is_null($this->newsletter)) {
            throw new \Exception("Newsletter with uid: " . $this->newsletter . " was not found", 1643821994);
        }
        if (empty($this->newsletter->getRecipientList())) {
            throw new \Exception("Newsletter does not have any recipients.", 1643822115);
        }

        if ($this->getTest()) {
            $recipientList = $this->newsletter->getTestRecipientList();
        } else {
            $recipientList = $this->newsletter->getRecipientList();
        }
        $recipients = $recipientList->getRecipients();

        if (empty($recipients)) {
            throw new Exception("Recipient list is empty", 1644851884);
        }


        $sendOut = GeneralUtility::makeInstance(SendOut::class);
        $sendOut->setNewsletter($this->newsletter);
        $sendOut->setTest($this->getTest());
        foreach ($recipients as $recipient) {
            /**@var $recipient RecipientInterface* */
            /**@var $mailTask MailTask* */
            $mailTask = GeneralUtility::makeInstance(MailTask::class);
            /** @TODO format needs to be configured somewhere */
            $mailTask->setFormat($mailTask::BOTH);
            $mailTask->setNewsletter($this->newsletter);
            $mailTask->setSendOut($sendOut);
            $mailTask->setRecipient($recipient->getUid());
            $mailTask->setPid($this->newsletter->getPid());
            $this->taskRepository->add($mailTask);
            $sendOut->addMailTask($mailTask);
        }
        $sendOut->setTotal(count($recipients));
        $sendOut->setPid($this->newsletter->getPid());
        $this->newsletter->addSendOut($sendOut);
        $this->newsletterRepository->update($this->newsletter);
        $this->sendOutRepository->add($sendOut);
        $this->persistenceManager->persistAll();
    }

    public function getTest(): bool
    {
        return (bool)$this->getProperty("test");
    }

    public function getNewsletter(): ?Newsletter
    {
        return $this->newsletter;
    }

    public function setNewsletter(?Newsletter $newsletter): self
    {
        $this->newsletter = $newsletter;
        return $this;
    }

    public function setTest(bool $test): void
    {
        $this->setProperty("test", $test);
    }

    public function getAdditionalData(): array
    {
        return [
            'newsletter' => $this->newsletter,
        ];
    }

}
