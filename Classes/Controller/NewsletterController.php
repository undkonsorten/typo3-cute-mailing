<?php
declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception as ExceptionDbalDriver;
use Exception;
use PharIo\Manifest\InvalidUrlException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\CuteMailing\Domain\Model\RecipientListInterface;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\RecipientListRepositoryInterface;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

/**
 * Class NewsletterController
 */
class NewsletterController extends ActionController
{

    /**
     * @var string
     */
    protected $receiverDetailFile = 'EXT:cute_mailing/Resources/Private/Templates/Newsletter/ReceiverDetail.html';

    /**
     * @var string
     */
    protected $wizardUserPreviewFile = 'EXT:cute_mailing/Resources/Private/Backend/Templates/Newsletter/WizardUserPreview.html';

    /**
     * @var NewsletterRepository|null
     */
    protected $newsletterRepository = null;

    /**
     * @var RecipientListRepositoryInterface|null
     */
    protected $recipientListRepository = null;

    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @param NewsletterRepository $newsletterRepository
     * @param RecipientListRepositoryInterface $recipientListRepository
     */
    public function __construct(
        NewsletterRepository             $newsletterRepository,
        RecipientListRepositoryInterface $recipientListRepository,
        TaskRepository                   $taskRepository,
        PersistenceManager               $persistenceManager
    )
    {
        $this->newsletterRepository = $newsletterRepository;
        $this->recipientListRepository = $recipientListRepository;
        $this->taskRepository = $taskRepository;
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @return void
     */
    public function listAction(): void
    {
        $currentPid = (int)GeneralUtility::_GP('id');
        if ($currentPid === 0) {
            $this->forward('choosePage');
        }
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentPid)->get();
        $newsletters = $this->newsletterRepository->findByRootline($rootline);
        $this->view->assignMultiple([
            'newsletters' => $newsletters,
        ]);
    }

    public function choosePageAction()
    {

    }

    public function editAction(Newsletter $newsletter)
    {
        $currentPid = (int)GeneralUtility::_GP('id');
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentPid)->get();
        $assign['recipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['testRecipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['newsletter'] = $newsletter;

        $this->view->assignMultiple($assign);
    }

    /**
     * @return void
     * @throws InvalidConfigurationTypeException
     * @noinspection PhpUnused
     */
    public function newAction(): void
    {
        $currentPid = (int)GeneralUtility::_GP('id');
        $pageTs = BackendUtility::getPagesTSconfig($currentPid);
        $page = BackendUtility::getRecord('pages', $currentPid);
        $assign['newsletterPage'] = $currentPid;
        $assign['title'] = $page['title'];
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentPid)->get();
        $assign['recipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['testRecipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['defaultSendingTime'] = new DateTime('now');


        //$pageTs = $this->getPageTsFromPage($currentPid);

        //Are default values set via pageTs?
        if (isset($pageTs['mod.']['web_modules.']['cute_mailing.'])) {
            $pageTs = $pageTs['mod.']['web_modules.']['cute_mailing.'];
            $assign['sender'] = $pageTs['sender'];
            $assign['senderName'] = $pageTs['sender_name'];
            $assign['replyTo'] = $pageTs['reply_to'];
            $assign['replyToName'] = $pageTs['reply_to_name'];
            $assign['pageTypeHtml'] = $pageTs['page_type_html'];
            $assign['pageTypeText'] = $pageTs['page_type_text'];
            $assign['allowedMarker'] = $pageTs['allowed_marker'];
        }

        $this->view->assignMultiple($assign);
    }

    /**
     * @return void
     * @throws ExceptionExtbaseObject
     * @throws InvalidConfigurationTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws InvalidUrlException
     * @throws NoSuchArgumentException
     * @throws InvalidArgumentNameException
     * @noinspection PhpUnused
     */
    public function initializeCreateAction(): void
    {
        $this->setDatetimeObjectInNewsletterRequest();
    }

    /**
     * @return void
     * @throws NoSuchArgumentException
     * @throws Exception
     */
    protected function setDatetimeObjectInNewsletterRequest(): void
    {
        $newsletter = (array)$this->request->getArgument('newsletter');
        if (!empty($newsletter['sendingTime'])) {
            $datetime = new DateTime($newsletter['sendingTime']);
        } else {
            $datetime = new DateTime();
        }
        $newsletter['sendingTime'] = $datetime;
        $this->request->setArgument('newsletter', $newsletter);
    }

    public function initializeAction()
    {
        $dateFormat = 'Y-m-d\TH:i';
        if (isset($this->arguments['newsletter'])) {
            $this->arguments['newsletter']
                ->getPropertyMappingConfiguration()
                ->forProperty('sendingTime')
                ->setTypeConverterOption(
                    DateTimeConverter::class,
                    DateTimeConverter::CONFIGURATION_DATE_FORMAT,
                    $dateFormat);
        }
    }

    /**
     * @param Newsletter $newsletter
     * @return void
     * @throws ExceptionExtbaseObject
     * @throws IllegalObjectTypeException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     * @throws StopActionException
     * @throws ExceptionDbalDriver
     */
    public function createAction(Newsletter $newsletter): void
    {
        $currentPid = (int)GeneralUtility::_GP('id');
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentPid)->get();
        foreach ($rootline as $page) {
            if ($page['doktype'] === 116) {
                $newsletter->setPid($page['uid']);
            }
        }
        $this->newsletterRepository->add($newsletter);
        $this->persistenceManager->persistAll();
        $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.create.message', 'cute_mailing'));
        $this->redirect('list');
    }

    /**
     * @param Newsletter $newsletter
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnknownObjectException
     * @noinspection PhpUnused
     */
    public function enableAction(Newsletter $newsletter): void
    {
        if ($newsletter->getStatus() >= $newsletter::SCHEDULED) {
            $this->addFlashMessage('Newsletter was already send.', 'Was sended.', AbstractMessage::ERROR);
        } else {
            $newsletter->enable();
            /**@var $newsletterTask NewsletterTask* */
            $newsletterTask = GeneralUtility::makeInstance(NewsletterTask::class);
            $newsletterTask->setNewsletter($newsletter);
            $newsletterTask->setStartDate($newsletter->getSendingTime()->getTimestamp());
            $this->taskRepository->add($newsletterTask);
            $this->newsletterRepository->update($newsletter);
            $this->addFlashMessage('Newsletter was queued for sending.', 'Sending....', AbstractMessage::OK);
        }

        $this->redirect('list');
    }

    public function updateAction(Newsletter $newsletter): void
    {
        $this->newsletterRepository->update($newsletter);
        $this->addFlashMessage('Newsletter was updated.', 'Updated', AbstractMessage::OK);
        $this->redirect('list');
    }

    /**
     * @param Newsletter $newsletter
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws DBALException
     */
    public function deleteAction(Newsletter $newsletter): void
    {
        $this->newsletterRepository->remove($newsletter);
        $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.delete.message', 'cute_mailing'), 'Deleted');
        $this->redirect('list');
    }

    public function sendTestMailAction(Newsletter $newsletter): void
    {
        if ($newsletter->getTestRecipientList()) {
            $newsletter->setStatus($newsletter::TESTED);
            /**@var $newsletterTask NewsletterTask* */
            $newsletterTask = GeneralUtility::makeInstance(NewsletterTask::class);
            $newsletterTask->setNewsletter($newsletter);
            $newsletterTask->setTest(true);

            $this->taskRepository->add($newsletterTask);
            $this->newsletterRepository->update($newsletter);
            $this->addFlashMessage('Your test mailing is being send out for the recipient group: ' . $newsletter->getTestRecipientList()->getName(), 'Testmailing invoked', AbstractMessage::OK);
        } else {
            $this->addFlashMessage('This newsletter has no test recipient.', 'Error', AbstractMessage::ERROR);
        }
        $this->redirect('list');

    }

    public function wizardUserPreviewAjax(ServerRequestInterface $request): ResponseInterface
    {
        $recipientListId = $request->getQueryParams()['recipientList'];
        /** @var RecipientListInterface $recipientList */
        $recipientList = $this->recipientListRepository->findByUid($recipientListId);
        $recipients = $recipientList->getRecipients();
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->wizardUserPreviewFile));
        $standaloneView->assignMultiple([
            'userPreview' => array_slice($recipients, 0, 3),
            'userAmount' => count($recipients),
        ]);
        /** @noinspection PhpComposerExtensionStubsInspection */
        return $this->jsonResponse(json_encode(['html' => $standaloneView->render()]));
    }

}
