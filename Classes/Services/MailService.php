<?php

namespace Undkonsorten\CuteMailing\Services;

use Symfony\Component\Mime\Part\DataPart;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\RecipientInterface;
use Undkonsorten\CuteMailing\Domain\Model\SendOut;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\SendOutRepository;

class MailService implements SingletonInterface
{

    /**
     * @var MailMessage|null
     */
    protected $email;

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

    /**
     * @var SendOutRepository
     */
    protected $sendOutRepository;

    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var FrontendInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $requestOptions;

    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    public function injectSendOutRepository(SendOutRepository $sendOutRepository)
    {
        $this->sendOutRepository = $sendOutRepository;
    }

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

    public function __construct(FrontendInterface $cache)
    {
        $this->cache = $cache;
    }



    public function sendMail(MailTask $mailTask)
    {
        /** @var Newsletter $newsletter */
        $newsletter = $this->newsletterRepository->findByUid($mailTask->getNewsletter());
        /** @var SendOut $sendOut */
        $sendOut = $this->sendOutRepository->findByUid($mailTask->getSendOut());
        if (is_null($newsletter)) {
            throw new \Exception('No newsletter given for sending', 1651441455);
        }

        // They are issue with recipient list type if different types for recipient list and test-recipient list are used!
        // Therefore check here is necessary. Maybe change in future if another solution is needed.
        if ($sendOut->isTest()) {
            /** @var RecipientInterface $recipient */
            $recipient = $newsletter->getTestRecipientList()->getRecipient($mailTask->getRecipient());
        } else {
            /** @var RecipientInterface $recipient */
            $recipient = $newsletter->getRecipientList()->getRecipient($mailTask->getRecipient());
        }
        $htmlCacheIdentifier = 'htmlContent_'.$sendOut->getUid();
        $textCacheIdentifier = 'textContent_'.$sendOut->getUid();

        $this->requestOptions = [
          'auth' => [
              $newsletter->getBasicAuthUser(),
              $newsletter->getBasicAuthPassword()
          ]
        ];


        if($mailTask->getFormat() == $mailTask::BOTH || $mailTask->getFormat() == $mailTask::HTML) {
            if (($htmlContent = $this->cache->get($htmlCacheIdentifier)) === false) {
                /** @var Site $site */
                $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($newsletter->getNewsletterPage());
                $htmlUrl = (string)$site->getRouter()->generateUri(
                    $newsletter->getNewsletterPage(),
                    ['type' => $newsletter->getPageTypeHtml(), '_language' => $newsletter->getLanguage()]
                );
                $htmlUrl = GeneralUtility::makeInstance(Uri::class, $htmlUrl);
                $htmlResponse = $this->requestFactory->request(
                    $htmlUrl,
                    'GET',
                    $this->requestOptions
                );
                $htmlContent = $htmlResponse->getBody()->getContents();
                $this->cache->set($htmlCacheIdentifier, $htmlContent);
            }
        }

        if($mailTask->getFormat() == $mailTask::BOTH || $mailTask->getFormat() == $mailTask::PLAINTEXT){
            if (($textContent = $this->cache->get($textCacheIdentifier)) === false) {
                /** @var Site $site */
                $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($newsletter->getNewsletterPage());
                $textUrl = (string)$site->getRouter()->generateUri(
                    $newsletter->getNewsletterPage(),
                    ['type' => $newsletter->getPageTypeText(), '_language' => $newsletter->getLanguage()]
                );
                $textUrl = GeneralUtility::makeInstance(Uri::class, $textUrl);
                $textResponse = $this->requestFactory->request(
                    $textUrl,
                    'GET',
                    $this->requestOptions
                );
                $textContent = $textResponse->getBody()->getContents();
                $this->cache->set($textCacheIdentifier, $textContent);
            }
        }


        /** @var MailMessage $email */
        $this->email = GeneralUtility::makeInstance(MailMessage::class);
	$recipientName = trim(sprintf('%s %s',$recipient->getFirstName(), $recipient->getLastName()));

        $this->email
            ->to(sprintf('%s <%s>',$recipientName, $recipient->getEmail()))
            ->from(sprintf('%s <%s>',$newsletter->getSenderName(),$newsletter->getSender()))
            ->replyTo(sprintf('%s <%s>',$newsletter->getReplyToName(),$newsletter->getReplyTo()))
            ->subject($newsletter->getSubject());

        $header = $this->email->getHeaders();
        // save the rcpt in the header
        $header->addTextHeader('X-TYPO3RCPT', base64_encode($recipient->getEmail()));
        // save the newsletter uid in the header
        $header->addTextHeader('X-TYPO3NLUID', $sendOut->getUid());

        if (trim($newsletter->getReturnPath())) {
            $this->email->returnPath($newsletter->getReturnPath());
        }

        if ($mailTask->getFormat() == $mailTask::HTML) {
            if ($mailTask->isAttachImages()) {
                $htmlContent = $this->attachImages($htmlContent);
            }
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $htmlContent, $recipient);
            $this->email->html($htmlContent);
        }
        if ($mailTask->getFormat() == $mailTask::PLAINTEXT) {
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $textContent, $recipient);
            $this->email->text($textContent);

        }
        if ($mailTask->getFormat() == $mailTask::BOTH) {
            if ($mailTask->isAttachImages()) {
                $htmlContent = $this->attachImages($htmlContent);
            }
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $htmlContent, $recipient);
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $textContent, $recipient);
            $this->email
                ->html($htmlContent)
                ->text($textContent);
        }
        $this->email->send();
        $sendOut->incrementCompleted();
        $newsletter->updateStatus();
        $this->newsletterRepository->update($newsletter);
        $this->sendOutRepository->update($sendOut);
    }

    protected function replaceMarker(array $allowedMarker, &$content, RecipientInterface $recipient)
    {
        foreach ($allowedMarker as $marker) {
            try {
                $property = ObjectAccess::getProperty($recipient, $marker);
            } catch (\Exception $exception) {
                $property = null;
                /**@todo maybe log this ot something */
            }

            if (!is_null($property)) {
                $content = str_replace(['###' . $marker . '###', '%23%23%23' . $marker . '%23%23%23'], [$property, $property], $content);
            }

        }
    }

    /**
     * This method looks for image tags and replaces their src attributes
     * with corresponding cid uris after downloading and attaching the
     * images.
     * For now, there‘s no possibility to control which images are attached.
     *
     * @param string $html
     * @return string
     */
    protected function attachImages(string $html): string
    {
        return preg_replace_callback(
            '/<img[^>]*>/i',
            function ($match) {
                return $this->replaceImageSource($match[0]);
            },
            $html
        );
    }

    protected function replaceImageSource(string $imageTag): string
    {
        return preg_replace_callback(
            '/src="([^"]*)"/',
            function ($match) {
                $part = $this->createImageMailPartFromUri($match[1]);
                $cid = $part->getContentId();
                $this->email->attachPart($part);
                return sprintf('src="cid:%s"', $cid);
            },
            $imageTag
        );
    }

    protected function createImageMailPartFromUri(string $uri): DataPart
    {
        $image = $this->requestFactory->request(
            $uri,
            'GET',
            $this->requestOptions
        );
        $imageContent = $image->getBody()->getContents();
        return new DataPart($imageContent, basename($uri), $image->getHeaderLine('Content-Type'));
    }

}
