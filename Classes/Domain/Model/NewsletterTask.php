<?php
namespace Undkonsorten\CuteMailing\Domain\Model;


use phpDocumentor\Reflection\Types\Boolean;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Services\RecipientService;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

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
        /**@var $newsletter Newsletter**/
        $newsletter = $this->newsletterRepository->findByUid($this->getNewsletter());
        if(is_null($newsletter)){
            throw new \Exception("Newsletter with uid: ".$this->getNewsletter()." was not found", 1643821994);
        }
        if(empty($newsletter->getRecipientList())){
            throw new \Exception("Newsletter does not have any recipients.",1643822115);
        }

        if($this->getTest()){
            $recipientList = $newsletter->getTestRecipientList();
        }else{
            $recipientList = $newsletter->getRecipientList();
        }
        $recipients = $recipientList->getRecipients();

        if(empty($recipients)){
            throw new Exception("Recipient list is empty",1644851884);
        }

        foreach ($recipients as $recipient){
            /**@var $recipient RecipientInterface**/
            /**@var $mailTask MailTask**/
            $mailTask = GeneralUtility::makeInstance(MailTask::class);
            /**@TODO format needs to be configured somewhere **/
            $mailTask->setFormat($mailTask::HTML);
            $mailTask->setNewsletter($newsletter->getUid());
            $mailTask->setRecipient($recipient->getUid());
            $mailTask->setPageTypeHtml($this->getPageTypeHtml());
            $mailTask->setPageTypeText($this->getPageTypeText());
            $this->taskRepository->add($mailTask);
        }
        $this->persistenceManager->persistAll();
    }

    public function getNewsletter(): int
    {
        return $this->getProperty("newsletter");
    }
    public function setNewsletter(int $newsletterUid): void
    {
        $this->setProperty("newsletter", $newsletterUid);
    }

    public function getTest(): bool
    {
        return (bool)$this->getProperty("test");
    }

    public function setTest(bool $test): void
    {
        $this->setProperty("test", $test);
    }

    public function getPageTypeHtml(): int
    {
        return (int)$this->getProperty('pageTypeHtml');
    }

    public function getPageTypeText(): int
    {
        return (int)$this->getProperty('pageTypeText');
    }

    public function setPageTypeText(int $value): void
    {
        $this->setProperty('pageTypeText', $value);
    }

    public function setPageTypeHtml(int $value): void
    {
        $this->setProperty('pageTypeHtml', $value);
    }
}
