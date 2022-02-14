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
        /** @var MailMessage $email */
        $email = GeneralUtility::makeInstance(MailMessage::class);
        /** @var SiteFinder $site */
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($mailTask->getNewsletterPage());
        $url = (string)$site->getRouter()->generateUri($mailTask->getNewsletterPage());
        $uri = GeneralUtility::makeInstance(Uri::class, $url);
        $uri = $uri->withQuery('type=10');
        $response = $this->requestFactory->request($uri);
        $email
            ->to($mailTask->getEmail())
            ->from('jeremy@acme.com')
            ->subject('TYPO3 loves you - here is why')
            ->html($response->getBody()->getContents())
            ->send();
    }
}
