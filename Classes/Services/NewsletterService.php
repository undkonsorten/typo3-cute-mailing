<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Services;

use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;

class NewsletterService
{

    /**
     * @var NewsletterRepository
     */
    protected $newsletterRepository;

    public function __construct(NewsletterRepository $newsletterRepository)
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    public function updateNewsletterStatus(Newsletter $newsletter)
    {
        if ($newsletter->getStatus() === Newsletter::SCHEDULED) {
            if ($newsletter->isComplete()) {
                $newsletter->setStatus(Newsletter::SENT);
                $this->newsletterRepository->update($newsletter);
            }
        }
    }

}
