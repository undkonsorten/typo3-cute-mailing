<?php
declare(strict_types = 1);
namespace Undkonsorten\CuteMailing\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception as ExceptionDbalDriver;
use Doctrine\DBAL\Exception as ExceptionDbal;
use Exception;
use In2code\Luxletter\Utility\BackendUserUtility;
use In2code\Luxletter\Utility\LocalizationUtility;
use PharIo\Manifest\InvalidUrlException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Object\Exception as ExceptionExtbaseObject;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\RecipientListRepositoryRepository;

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
     * @var NewsletterRepository|null
     */
    protected $newsletterRepository = null;

    /**
     * @var RecipientListRepositoryRepository|null
     */
    protected $recipientListRepository = null;



    /**
     * @param NewsletterRepository $newsletterRepository
     * @param RecipientListRepositoryRepository $recipientListRepository
     */
    public function __construct(
        NewsletterRepository    $newsletterRepository,
        RecipientListRepositoryRepository $recipientListRepository
    ) {
        $this->newsletterRepository = $newsletterRepository;
        $this->recipientListRepository = $recipientListRepository;
    }

    /**
     * @return void
     */
    public function listAction(): void
    {
        $this->view->assignMultiple([
            'newsletters' => $this->newsletterRepository->findAll(),
            'recipientLists' => $this->recipientListRepository->findAll()
        ]);
    }

    /**
     * @return void
     * @throws InvalidConfigurationTypeException
     * @noinspection PhpUnused
     */
    public function newAction(): void
    {
        $this->view->assignMultiple([
            'configurations' => $this->recipientListRepository->findAll(),
            'layouts' => $this->layoutService->getLayouts()
        ]);
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
        $this->newsletterRepository->add($newsletter);
        $this->newsletterRepository->persistAll();
        $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.create.message'));
        $this->redirect('list');
    }

    /**
     * @param Newsletter $newsletter
     * @return void
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnknownObjectException
     */
    public function disableAction(Newsletter $newsletter): void
    {
        $newsletter->disable();
        $this->newsletterRepository->update($newsletter);
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
        $newsletter->enable();
        $this->newsletterRepository->update($newsletter);
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
        $this->newsletterRepository->removeNewsletterAndQueues($newsletter);
        $this->addFlashMessage(LocalizationUtility::translate('module.newsletter.delete.message'));
        $this->redirect('list');
    }




    /**
     * @return void
     * @throws NoSuchArgumentException
     * @throws Exception
     */
    protected function setDatetimeObjectInNewsletterRequest(): void
    {
        $newsletter = (array)$this->request->getArgument('newsletter');
        if (!empty($newsletter['datetime'])) {
            $datetime = new DateTime($newsletter['datetime']);
        } else {
            $datetime = new DateTime();
        }
        $newsletter['datetime'] = $datetime;
        $this->request->setArgument('newsletter', $newsletter);
    }
}
