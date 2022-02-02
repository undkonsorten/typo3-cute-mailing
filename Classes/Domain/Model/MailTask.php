<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use Symfony\Component\Mailer\Mailer;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Undkonsorten\Taskqueue\Domain\Model\Task;

class MailTask extends Task
{


    /**
     * @var string
     */
    protected $configuration;


    public function run(): void
    {
        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to($this->getProperty('email'))
            ->from(new Address('jeremy@acme.com', 'Jeremy'))
            ->subject('TYPO3 loves you - here is why')
            ->format('both') // send HTML and plaintext mail
           # ->setTemplate('TipsAndTricks')
            ->assign('mySecretIngredient', 'Tomato and TypoScript');
        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }


    public function setEmail(string $email): void
    {
        $this->setProperty("email", $email);
    }

    public function getEmail(): int
    {
        return $this->getProperty("email");
    }

   public function setNewsletterPage(int $newsletterPage): void
   {
       $this->setProperty("newsletterPage", $newsletterPage);
   }

   public function getNewsletterPage(): int
   {
       return $this->getProperty("newsletterPage");
   }

    public function setConfiguration(string $configuration): void
    {
        $this->setProperty("configuration", $configuration);
    }

    public function getConfiguration(): string
    {
        return $this->getProperty("configuration");
    }

}
