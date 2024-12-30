<?php

namespace Ziadoz\AssertableHtml\Matchers;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Contracts\AssertableElementInterface;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Elements\AssertableFormElement;

class AssertableElementMatcher
{
    /** The assertable element classes. */
    protected const array ELEMENTS = [
        AssertableFormElement::class,
    ];

    /** Match the element to the applicable assertable element class. */
    public function match(HTMLElement|Element $element): AssertableElementInterface
    {
        foreach (static::ELEMENTS as $class) {
            if ($class::matches($element)) {
                return new $class($element);
            }
        }

        return new AssertableElement($element);
    }
}
