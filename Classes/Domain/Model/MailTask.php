<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


use Undkonsorten\CuteMailing\Services\MailService;
use Undkonsorten\Taskqueue\Domain\Model\Task;

class MailTask extends Task
{

    const PLAINTEXT = 'plain';
    const HTML = 'html';
    const BOTH = 'both';


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

    public function setFormat(string $format): void
    {
        $this->setProperty("format", $format);
    }

    public function getFormat(): string
    {
        return $this->getProperty("format");
    }

}
