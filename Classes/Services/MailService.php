<?php

namespace Undkonsorten\CuteMailing\Services;

use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\RecipientInterface;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;

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
        $newsletter = $mailTask->getNewsletter();
        if (is_null($newsletter)) {
            throw new \Exception('No newsletter given for sending', 1651441455);
        }

        /** @var RecipientInterface $recipient */
        $recipient = $newsletter->getRecipientList()->getRecipient($mailTask->getRecipient());

        /** @var MailMessage $email */
        $email = GeneralUtility::makeInstance(MailMessage::class);
        /** @var SiteFinder $site */
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($newsletter->getNewsletterPage());
        $url = (string)$site->getRouter()->generateUri($newsletter->getNewsletterPage());
        $uri = GeneralUtility::makeInstance(Uri::class, $url);

        if ($mailTask->getFormat() == $mailTask::HTML) {
            $uri = $uri->withQuery('type=' . $newsletter->getPageTypeHtml());
            $response = $this->requestFactory->request($uri);
            $content = $response->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $content, $recipient);
            $email
                ->to($recipient->getEmail())
                ->from($newsletter->getSender())
                ->replyTo($newsletter->getReplyTo())
                ->subject($newsletter->getSubject())
                ->html($content)
                ->send();
        }

    }

    protected function replaceMarker(array $allowedMarker, &$content, RecipientInterface $recipient)
    {
        foreach ($allowedMarker as $marker) {
            try {
                $property = ObjectAccess::getProperty($recipient, $marker);
            } catch (\Exception $exception) {
                /**@todo maybe log this ot something */
            }

            if (!is_null($property)) {
                $content = str_replace('###' . $marker . '###', $property, $content);
            }

        }

    }
}
