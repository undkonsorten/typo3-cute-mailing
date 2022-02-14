<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


use Undkonsorten\CuteMailing\Services\MailService;
use Undkonsorten\Taskqueue\Domain\Model\Task;

class MailTask extends Task
{


    /**
     * @var string
     */
    protected $configuration;

    /**
     * @var MailService
     */
    protected $mailService;

    public function injectMailService(MailService $mailService)
    {
        $this->mailService = $mailService;
    }


    public function run(): void
    {
        $this->mailService->sendMail($this);
    }


    public function setEmail(string $email): void
    {
        $this->setProperty("email", $email);
    }

    public function getEmail(): string
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
