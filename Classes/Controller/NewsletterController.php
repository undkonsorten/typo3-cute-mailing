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
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Site\Entity\Site;
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
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @param NewsletterRepository $newsletterRepository
     * @param RecipientListRepositoryInterface $recipientListRepository
     */
    public function __construct(
        NewsletterRepository             $newsletterRepository,
        RecipientListRepositoryInterface $recipientListRepository,
        TaskRepository                   $taskRepository,
        PersistenceManager               $persistenceManager,
        PageRepository                   $pageRepository
    )
    {
        $this->newsletterRepository = $newsletterRepository;
        $this->recipientListRepository = $recipientListRepository;
        $this->taskRepository = $taskRepository;
        $this->persistenceManager = $persistenceManager;
        $this->pageRepository = $pageRepository;
    }

    /**
     *
     */
    public function listAction(): ResponseInterface
    {
        $currentPid = (int)GeneralUtility::_GP('id');
        if ($currentPid === 0) {
            return new ForwardResponse('choosePage');
        }
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentPid)->get();
        $newsletters = $this->newsletterRepository->findByRootline($rootline);
        $this->view->assignMultiple([
            'newsletters' => $newsletters,
        ]);
        return $this->htmlResponse();
    }

    public function choosePageAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function prepareAction(): ResponseInterface
    {
        $assign = [];
        $currentPid = $this->getCurrentPageUid();
        $assign['newsletterPage'] = $currentPid;
        $siteLanguages = $this->getSiteLanguagesForPid($currentPid);

        $pageTs = $this->getPageTsConfigForModule($currentPid);

        $assign['displayLanguageSelect'] = true;
        if (isset($pageTs['hideLanguageSelection'])) {
            $assign['displayLanguageSelect'] = !$pageTs['hideLanguageSelection'];
        }

        if (count($siteLanguages) < 2) {
            $assign['displayLanguageSelect'] = false;
        }
        $assign['languages'] = $siteLanguages;
        $assign['selectedLanguage'] = $pageTs['language'] ?? 0;
        $this->view->assignMultiple($assign);
        return $this->htmlResponse();
    }

    public function editAction(Newsletter $newsletter): ResponseInterface
    {
        $currentPid = $this->getCurrentPageUid();
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentPid)->get();
        $assign['recipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['testRecipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['newsletter'] = $newsletter;

        $this->view->assignMultiple($assign);
        return $this->htmlResponse();
    }

    /**
     * @return void
     * @throws InvalidConfigurationTypeException
     * @noinspection PhpUnused
     */
    public function newAction(?int $newsletterPage = null, ?int $language = null): ResponseInterface
    {
        $currentPid = $this->getCurrentPageUid();
        $pageTs = $this->getPageTsConfigForModule($currentPid);

        $page = BackendUtility::getRecord('pages', $newsletterPage ?? $currentPid );

        $language = $language ?? (int)$pageTs['language'];
        if ($this->shouldForwardToPrepareAction($currentPid, $page, $pageTs, $newsletterPage, $language)) {
            return new ForwardResponse('prepare');
        }
        if($language > 0){
            $page = $this->pageRepository->getPageOverlay($page, $language);
            if($page['sys_language_uid'] !== $language){
                $this->addFlashMessage(
                    LocalizationUtility::translate('module.newsletter.noTranslation.message', 'cute_mailing'),
                    LocalizationUtility::translate('module.newsletter.noTranslation.title', 'cute_mailing'),
                    AbstractMessage::WARNING
                );
            }
        }
        $assign['newsletterPage'] = $newsletterPage ?? $currentPid;
        $assign['language'] = $language ?? $pageTs['language'] ?? 0;
        $assign['title'] = $page['title'];
        $assign['subject'] = $page['title'];
        $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $currentPid)->get();
        $assign['recipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['testRecipientList'] = $this->recipientListRepository->findByRootline($rootline);
        $assign['defaultSendingTime'] = new DateTime('now');


        $assign['sender'] = $pageTs['sender'];
        $assign['senderName'] = $pageTs['sender_name'];
        $assign['replyTo'] = $pageTs['reply_to'];
        $assign['replyToName'] = $pageTs['reply_to_name'];
        $assign['pageTypeHtml'] = $pageTs['page_type_html'];
        $assign['pageTypeText'] = $pageTs['page_type_text'];
        $assign['allowedMarker'] = $pageTs['allowed_marker'];
        $assign['returnPath'] = $pageTs['return_path'];

        $this->view->assignMultiple($assign);
        return $this->htmlResponse();
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
            if ($page['module'] === 'cute_mailing') {
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
            $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.newsletterSend.message', 'cute_mailing'), LocalizationUtility::translate('module.newsletter.newsletterSend.title', 'cute_mailing'), AbstractMessage::ERROR);
        } else {
            $currentPid = $this->getCurrentPageUid();
            $pageTs = $this->getPageTsConfigForModule($currentPid);
            $newsletter->enable();
            /**@var $newsletterTask NewsletterTask* */
            $newsletterTask = GeneralUtility::makeInstance(NewsletterTask::class);
            $newsletterTask->setNewsletter($newsletter);
            $newsletterTask->setStartDate($newsletter->getSendingTime()->getTimestamp());
            $newsletterTask->setRecipientListChunkSize((int)$pageTs['recipient_chunk_size']);
            $this->taskRepository->add($newsletterTask);
            $this->newsletterRepository->update($newsletter);
            $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.newsletterQueued.message', 'cute_mailing'), LocalizationUtility::translate('module.newsletter.newsletterQueued.title', 'cute_mailing'), AbstractMessage::OK);
        }

        $this->redirect('list');
    }

    public function updateAction(Newsletter $newsletter): void
    {
        $this->newsletterRepository->update($newsletter);
        $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.newsletterUpdated.message', 'cute_mailing'), LocalizationUtility::translate('module.newsletter.newsletterUpdated.title', 'cute_mailing'), AbstractMessage::OK);
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

    public function sendTestMailAction(Newsletter $newsletter, bool $attachImages = false): void
    {
        if ($newsletter->getTestRecipientList()) {
            $currentPid = $this->getCurrentPageUid();
            $pageTs = $this->getPageTsConfigForModule($currentPid);
            $newsletter->setStatus($newsletter::TESTED);
            /**@var $newsletterTask NewsletterTask* */
            $newsletterTask = GeneralUtility::makeInstance(NewsletterTask::class);
            $newsletterTask->setNewsletter($newsletter);
            $newsletterTask->setTest(true);
            $newsletterTask->setAttachImages($attachImages);
            $newsletterTask->setRecipientListChunkSize((int)$pageTs['recipient_chunk_size']);

            $this->taskRepository->add($newsletterTask);
            $this->newsletterRepository->update($newsletter);
            $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.testMailToGroup.message', 'cute_mailing') . $newsletter->getTestRecipientList()->getName(), LocalizationUtility::translate('module.newsletter.testMailToGroup.title', 'cute_mailing'), AbstractMessage::OK);
        } else {
            $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.testMailNoRecipient.message', 'cute_mailing'), LocalizationUtility::translate('module.newsletter.testMailNoRecipient.title', 'cute_mailing'), AbstractMessage::ERROR);
        }
        $this->redirect('list');

    }

    public function wizardUserPreviewAjax(ServerRequestInterface $request): ResponseInterface
    {
        $recipientListId = $request->getQueryParams()['recipientList'];
        /** @var RecipientListInterface $recipientList */
        $recipientList = $this->recipientListRepository->findByUid($recipientListId);
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->wizardUserPreviewFile));
        $standaloneView->assignMultiple([
            'userPreview' => $recipientList->getRecipients(3),
            'userAmount' => $recipientList->getRecipientsCount(),
        ]);
        /** @noinspection PhpComposerExtensionStubsInspection */
        return $this->jsonResponse(json_encode(['html' => $standaloneView->render()]));
    }

    protected function getCurrentPageUid(): int
    {
        return (int)GeneralUtility::_GP('id');
    }

    protected function shouldForwardToPrepareAction(int $currentPid, ?array $page, array $pageTs, ?int $newsletterPage, ?int $language): bool
    {
        if ($page['doktype'] === PageRepository::DOKTYPE_SYSFOLDER) {
            /** @TODO Translate error messages * */
            $this->addFlashMessage(
                LocalizationUtility::translate('module.newsletter.preparePage.wrongType.message', 'cute_mailing'),
                LocalizationUtility::translate('module.newsletter.preparePage.wrongType.title', 'cute_mailing'),
                AbstractMessage::WARNING
            );
            return true;
        }
        if ($newsletterPage !== null && $language !== null) {
            return false;
        }

        $hideLanguageSelection = $pageTs['hideLanguageSelection'] ?? false;
        if ($hideLanguageSelection) {
            return false;
        }

        $languages = $this->getSiteLanguagesForPid($newsletterPage ?? $currentPid);
        /** @noinspection IfReturnReturnSimplificationInspection */
        if (count($languages) < 2) {
            return false;
        }
        return true;
    }

    protected function getSiteLanguagesForPid(int $currentPid): array
    {
        /** @var Site $site */
        $site = $this->request->getAttribute('site');
        return $site->getLanguages();
    }

    protected function getPageTsConfigForModule(?int $pid = null): array
    {
        $pageTsDefault = [
            'language' => null,
            'sender' => '',
            'sender_name' => '',
            'reply_to' => '',
            'reply_to_name' => '',
            'page_type_html' => '',
            'page_type_text' => '',
            'allowed_marker' => '',
            'return_path' => '',
            'recipient_chunk_size' => 0
        ];
        $pageTs = array_merge(
            $pageTsDefault,
            BackendUtility::getPagesTSconfig($pid ?? $this->getCurrentPageUid())['mod.']['web_modules.']['cute_mailing.'] ?? []
        );
        return $pageTs;
    }

}
