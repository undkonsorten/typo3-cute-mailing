<?php

namespace Undkonsorten\CuteMailing\Services;

use Symfony\Component\PropertyAccess\Exception\InvalidPropertyPathException;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
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
        /** @var Site $site */
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($newsletter->getNewsletterPage());
        $htmlUrl = (string)$site->getRouter()->generateUri($newsletter->getNewsletterPage(), ['type' => $newsletter->getPageTypeHtml()]);
        $textUrl = (string)$site->getRouter()->generateUri($newsletter->getNewsletterPage(), ['type' => $newsletter->getPageTypeText()]);
        $htmlUrl = GeneralUtility::makeInstance(Uri::class, $htmlUrl);
        $textUrl = GeneralUtility::makeInstance(Uri::class, $textUrl);

        $email
            ->to($recipient->getEmail())
            ->from($newsletter->getSender())
            ->replyTo($newsletter->getReplyTo())
            ->subject($newsletter->getSubject());

        if ($newsletter->getReturnPath() !== null) {
            $email->returnPath($newsletter->getReturnPath());
        }

        if ($mailTask->getFormat() == $mailTask::HTML) {
            $response = $this->requestFactory->request($htmlUrl);
            $content = $response->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $content, $recipient);
            $email->html($content);
        }
        if ($mailTask->getFormat() == $mailTask::PLAINTEXT) {
            $response = $this->requestFactory->request($textUrl);
            $content = $response->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $content, $recipient);
            $email->text($content);

        }
        if ($mailTask->getFormat() == $mailTask::BOTH) {
            $htmlResponse = $this->requestFactory->request($htmlUrl);
            $textResponse = $this->requestFactory->request($textUrl);
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
