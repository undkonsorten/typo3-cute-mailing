<?php
namespace Undkonsorten\CuteMailing\Services;

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;

class MailService implements SingletonInterface
{
    public function sendMail(MailTask $mailTask)
    {
        /** @var MailMessage $email */
        $email = GeneralUtility::makeInstance(MailMessage::class);
        $email
            ->to($mailTask->getEmail())
            ->from('jeremy@acme.com')
            ->subject('TYPO3 loves you - here is why')
            ->text('Ich bin der body')
            ->send();
    }
}
