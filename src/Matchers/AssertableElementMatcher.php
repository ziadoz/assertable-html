<?php

namespace Ziadoz\AssertableHtml\Matchers;

use Dom\Document;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Elements\AssertableFormElement;

class AssertableElementMatcher
{
    /** The assertable element classes. */
    protected const array ELEMENTS = [
        AssertableFormElement::class,
    ];

    /** Match the element to the applicable assertable element class. */
    public function match(HTMLDocument|Document|HTMLElement|Element $element): string
    {
        foreach (static::ELEMENTS as $class) {
            if ($class::matches($element)) {
                return $class;
            }
        }

        return AssertableElement::class;
    }
}
