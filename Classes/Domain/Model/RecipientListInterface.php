<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

interface RecipientListInterface
{
    public function getRecipients(): array;
}
