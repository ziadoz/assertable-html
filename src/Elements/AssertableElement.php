<?php

namespace Ziadoz\AssertableHtml\Elements;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\AssertableHtml;
use Ziadoz\AssertableHtml\Contracts\MatchableInterface;

class AssertableElement extends AssertableHtml implements MatchableInterface
{
    /** Return if the given element matches. */
    public static function matches(Element|HTMLElement $element): bool
    {
        return true;
    }
}
