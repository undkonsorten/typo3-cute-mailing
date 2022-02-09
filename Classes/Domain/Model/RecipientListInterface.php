<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

interface RecipientListInterface
{
    public function getRecipients(): array;

    public function getName(): string;

    public function setName(string $name);
}
