<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Contracts;

use Dom\Element;
use Dom\HTMLElement;

interface PromotableAssertableElement
{
    /** Return if the HTML element should be promoted by this element-specific assertable element. */
    public static function shouldPromote(HTMLElement|Element $element): bool;
}
