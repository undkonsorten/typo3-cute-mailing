<?php

namespace Undkonsorten\CuteMailing\Services;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\CuteMailing\Domain\Model\RecipientInterface;
use Undkonsorten\CuteMailing\Domain\Model\SendOut;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\SendOutRepository;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

class NewsletterService implements SingletonInterface
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
    public function __construct(protected \Undkonsorten\CuteMailing\Domain\Repository\SendOutRepository $sendOutRepository, \Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository $newsletterRepository, \Undkonsorten\Taskqueue\Domain\Repository\TaskRepository $taskRepository, \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager)
    {
        $this->newsletterRepository = $newsletterRepository;
        $this->taskRepository = $taskRepository;
        $this->persistenceManager = $persistenceManager;
    }

    public function runNewsletter(NewsletterTask $newsletterTask): void{
        $this->newsletter = $newsletterTask->getNewsletter();
        if (is_null($this->newsletter)) {
            throw new \Exception("Newsletter with uid: " . $this->newsletter . " was not found", 1643821994);
        }
        if (empty($this->newsletter->getRecipientList())) {
            throw new \Exception("Newsletter does not have any recipients.", 1643822115);
        }

        if ($newsletterTask->getTest()) {
            $recipientList = $this->newsletter->getTestRecipientList();
        } else {
            $recipientList = $this->newsletter->getRecipientList();
        }
        $recipients = $recipientList->getRecipients();

        if (empty($recipients)) {
            throw new Exception("Recipient list is empty", 1644851884);
        }


        /** @var SendOut $sendOut */
        $sendOut = GeneralUtility::makeInstance(SendOut::class);
        $sendOut->setNewsletter($this->newsletter);
        $sendOut->setTest($newsletterTask->getTest());
        // We need an uid for later
        $this->sendOutRepository->add($sendOut);
        $this->persistenceManager->persistAll();

        foreach ($recipients as $recipient) {
            /**@var $recipient RecipientInterface* */
            /**@var $mailTask MailTask* */
            $mailTask = GeneralUtility::makeInstance(MailTask::class);
            /** @TODO format needs to be configured somewhere */
            $mailTask->setFormat($mailTask::BOTH);
            $mailTask->setNewsletter($this->newsletter->getUid());
            $mailTask->setSendOut($sendOut->getUid());
            $mailTask->setRecipient($recipient->getUid());
            $mailTask->setPid($this->newsletter->getPid());
            $mailTask->setAttachImages($newsletterTask->isAttachImages() ?? false);
            $this->taskRepository->add($mailTask);
            $sendOut->addMailTask($mailTask);
        }
        $sendOut->setTotal(count($recipients));
        $sendOut->setPid($this->newsletter->getPid());
        $this->newsletter->addSendOut($sendOut);
        $this->newsletterRepository->update($this->newsletter);
        $this->persistenceManager->persistAll();
    }

}