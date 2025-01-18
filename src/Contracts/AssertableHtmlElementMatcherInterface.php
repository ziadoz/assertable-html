<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Contracts;

use Dom\Element;
use Dom\HTMLElement;

interface AssertableHtmlElementMatcherInterface
{
    /** Return if the assertable is usable for the given element. */
    public static function match(HTMLElement|Element $element): bool;
}
