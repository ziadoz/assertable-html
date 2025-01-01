<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Contracts;

use Dom\Element;
use Dom\HTMLElement;

interface MatchableInterface
{
    /** Return if the given element matches. */
    public static function matches(HTMLElement|Element $element): bool;
}
