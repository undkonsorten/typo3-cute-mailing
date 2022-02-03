<?php
namespace Undkonsorten\CuteMailing\Tests\Functional;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;

class SendMailTest extends FunctionalTestCase
{
    /**
     * @var array Have motion loaded
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/taskqueue'
    ];
    public function mailIsCreatedCorrectly(): void
    {
        /**@var $address \Undkonsorten\CuteMailing\Domain\Model\RecipientInterface*/
        $address1 = GeneralUtility::makeInstance(\Undkonsorten\CuteMailing\Domain\Model\RecipientInterface::class);
        $address1->setEmail('peter@test.de');
        $address1->setFirstName('Peter');
        $address1->setLastName('Müller');

        /**@var $address \Undkonsorten\CuteMailing\Domain\Model\RecipientInterface*/
        $address2 = GeneralUtility::makeInstance(\Undkonsorten\CuteMailing\Domain\Model\RecipientInterface::class);
        $address2->setEmail('gerd@test.de');
        $address2->setFirstName('Gerd');
        $address2->setLastName('Hermann');

        $addresses[] = $address1;
        $addresses[] = $address2;

        /**@var $newsletter Newsletter**/
        $newsletter = GeneralUtility::makeInstance(Newsletter::class);
        $newsletter->setNewsletterPage(12);
        $newsletter->setRecipientList($addresses);

        /**@var $newsletterTask \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask**/
        $newsletterTask = GeneralUtility::makeInstance(NewsletterTask::class);
        $newsletterTask->setNewsletter($newsletter);

    }

}
