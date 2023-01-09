<?php

namespace Undkonsorten\CuteMailing\Services;

use Symfony\Component\Mime\Part\DataPart;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\RecipientInterface;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;

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

        // They are issue with recipient list type if different types for recipient list and test-recipient list are used!
        // Therefore check here is necessary. Maybe change in future if another solution is needed.
        if ($mailTask->getSendOut()->isTest()) {
            /** @var RecipientInterface $recipient */
            $recipient = $newsletter->getTestRecipientList()->getRecipient($mailTask->getRecipient());
        } else {
            /** @var RecipientInterface $recipient */
            $recipient = $newsletter->getRecipientList()->getRecipient($mailTask->getRecipient());
        }

        /** @var MailMessage $email */
        $this->email = GeneralUtility::makeInstance(MailMessage::class);
        /** @var Site $site */
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($newsletter->getNewsletterPage());
        $htmlUrl = (string)$site->getRouter()->generateUri($newsletter->getNewsletterPage(), ['type' => $newsletter->getPageTypeHtml()]);
        $textUrl = (string)$site->getRouter()->generateUri($newsletter->getNewsletterPage(), ['type' => $newsletter->getPageTypeText()]);
        $htmlUrl = GeneralUtility::makeInstance(Uri::class, $htmlUrl);
        $textUrl = GeneralUtility::makeInstance(Uri::class, $textUrl);

        $this->email
            ->to($recipient->getEmail())
            ->from($newsletter->getSender())
            ->replyTo($newsletter->getReplyTo())
            ->subject($newsletter->getSubject());

        if (trim($newsletter->getReturnPath())) {
            $email->returnPath($newsletter->getReturnPath());
        }

        if ($mailTask->getFormat() == $mailTask::HTML) {
            $response = $this->requestFactory->request($htmlUrl);
            $content = $response->getBody()->getContents();
            if ($mailTask->isAttachImages()) {
                $content = $this->attachImages($content);
            }
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $content, $recipient);
            $this->email->html($content);
        }
        if ($mailTask->getFormat() == $mailTask::PLAINTEXT) {
            $response = $this->requestFactory->request($textUrl);
            $content = $response->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $content, $recipient);
            $this->email->text($content);

        }
        if ($mailTask->getFormat() == $mailTask::BOTH) {
            $htmlResponse = $this->requestFactory->request($htmlUrl);
            $textResponse = $this->requestFactory->request($textUrl);
            $htmlContent = $htmlResponse->getBody()->getContents();
            if ($mailTask->isAttachImages()) {
                $htmlContent = $this->attachImages($htmlContent);
            }
            $textContent = $textResponse->getBody()->getContents();
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $htmlContent, $recipient);
            $this->replaceMarker(GeneralUtility::trimExplode(',', $newsletter->getAllowedMarker()), $textContent, $recipient);
            $this->email
                ->html($htmlContent)
                ->text($textContent);
        }
        $this->email->send();

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
        $image = $this->requestFactory->request($uri);
        $imageContent = $image->getBody()->getContents();
        return new DataPart($imageContent, basename($uri), $image->getHeaderLine('Content-Type'));
    }

}
