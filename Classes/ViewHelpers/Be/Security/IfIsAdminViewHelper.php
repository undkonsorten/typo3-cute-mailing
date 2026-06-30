<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Undkonsorten\CuteMailing\ViewHelpers\Be\Security;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * This ViewHelper returns true if be user is amdin
 */
final class IfIsAdminViewHelper extends AbstractConditionViewHelper
{
    #[\Override]
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        /** @var BackendUserAuthentication $backenduser */
        $backenduser = $GLOBALS['BE_USER'];
        $isAdmin = $backenduser?->isAdmin();
        return (bool)$isAdmin;
    }
}
