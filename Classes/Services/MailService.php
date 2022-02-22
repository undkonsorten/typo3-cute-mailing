<?php
namespace Undkonsorten\CuteMailing\Services;

use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Http\UriFactory;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\RecipientInterface;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\RecipientRepositoryInterface;

class MailService implements SingletonInterface
{
    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var NewsletterRepository
     */
    protected $newsletterRepository;


    public function injectNewsletterRepository(NewsletterRepository $newsletterRepository)
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    public function injectRequestFactory(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    public function injectUriBuilder(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    public function sendMail(MailTask $mailTask)
    {
        /** @var Newsletter $newsletter */
        $newsletter = $this->newsletterRepository->findByUid($mailTask->getNewsletter());

        /** @var RecipientInterface $recipient */
        $recipient = $newsletter->getRecipientList()->getRecipient($mailTask->getRecipient());

        /** @var MailMessage $email */
        $email = GeneralUtility::makeInstance(MailMessage::class);
        /** @var SiteFinder $site */
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($newsletter->getNewsletterPage());
        $url = (string)$site->getRouter()->generateUri($newsletter->getNewsletterPage());
        $uri = GeneralUtility::makeInstance(Uri::class, $url);



        if($mailTask->getFormat() == $mailTask::HTML){
            $uri = $uri->withQuery('type='.$mailTask->getPageTypeHtml());
            $response = $this->requestFactory->request($uri);
            $email
                ->to($recipient->getEmail())
                ->from($newsletter->getSender())
                ->replyTo($newsletter->getReplyTo())
                ->subject($newsletter->getSubject())
                ->html($response->getBody()->getContents())
                ->send();
        }

    }
}
