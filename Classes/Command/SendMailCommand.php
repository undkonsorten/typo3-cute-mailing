<?php

namespace Undkonsorten\CuteMailing\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\RecipientListRepository;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

class SendMailCommand extends Command
{

    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var PersistenceManager
     */
    protected $persitenceManager;

    /**
     * @var NewsletterRepository;
     */
    protected $newsletterRepository;

    /**
     * @var RecipientListRepository
     */
    protected $recipientListRepository;

    public function __construct(
        TaskRepository          $taskRepository,
        PersistenceManager      $persistenceManager,
        NewsletterRepository    $newsletterRepository,
        RecipientListRepository $recipientsListRepository,
        string                  $name = null
    )
    {
        $this->taskRepository = $taskRepository;
        $this->persitenceManager = $persistenceManager;
        $this->newsletterRepository = $newsletterRepository;
        $this->recipientListRepository = $recipientsListRepository;
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->setDescription('Sends out test emails');

    }


    public  function execute(InputInterface $input, OutputInterface $output)
    {

        $recipients = $this->recipientListRepository->findByUid(1);

        /**@var $newsletter Newsletter**/
        $newsletter = GeneralUtility::makeInstance(Newsletter::class);
        $newsletter->setNewsletterPage(12);
        $newsletter->setRecipientList($recipients);
        $this->newsletterRepository->add($newsletter);
        $this->persitenceManager->persistAll();

        /**@var $newsletterTask \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask**/
        $newsletterTask = GeneralUtility::makeInstance(NewsletterTask::class);
        $newsletterTask->setNewsletter($newsletter->getUid());

        $this->taskRepository->add($newsletterTask);
        $this->persitenceManager->persistAll();
        return self::SUCCESS;

    }
}
