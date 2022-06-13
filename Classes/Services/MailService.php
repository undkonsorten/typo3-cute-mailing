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
        /** @noinspection PhpUndefinedMethodInspection */
        $url = (string)$site->getRouter()->generateUri($newsletter->getNewsletterPage());
        $uri = GeneralUtility::makeInstance(Uri::class, $url);

        $email
            ->to($recipient->getEmail())
            ->from($newsletter->getSender())
            ->replyTo($newsletter->getReplyTo())
            ->subject($newsletter->getSubject());

        if ($mailTask->getFormat() == $mailTask::HTML) {
            $uri = $uri->withQuery('type=' . $newsletter->getPageTypeHtml());
            $response = $this->requestFactory->request($uri);
            $content = $response->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $content, $recipient);
            $email->html($content);
        }
        if ($mailTask->getFormat() == $mailTask::PLAINTEXT) {
            $uri = $uri->withQuery('type=' . $newsletter->getPageTypeText());
            $response = $this->requestFactory->request($uri);
            $content = $response->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $content, $recipient);
            $email->text($content);

        }
        if ($mailTask->getFormat() == $mailTask::BOTH) {
            $htmlUri = $uri->withQuery('type=' . $newsletter->getPageTypeHtml());
            $textUri = $uri->withQuery('type=' . $newsletter->getPageTypeText());
            $htmlResponse = $this->requestFactory->request($htmlUri);
            $textResponse = $this->requestFactory->request($textUri);
            $htmlContent = $htmlResponse->getBody()->getContents();
            $textContent = $textResponse->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $htmlContent, $recipient);
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $textContent, $recipient);
            $email
                ->html($htmlContent)
                ->text($textContent);
        }
        $email->send();

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
}
