<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Matchers;

use Dom\Document;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Elements\AssertableFormElement;
use Ziadoz\AssertableHtml\Elements\AssertableInputElement;

class AssertableElementMatcher
{
    /** The assertable element classes. */
    protected const array ELEMENTS = [
        AssertableFormElement::class,
        AssertableInputElement::class,
    ];

    /** Match the element to the applicable assertable element class. */
    public function match(HTMLDocument|HTMLElement $element): string
    {
        foreach (static::ELEMENTS as $class) {
            if ($class::matches($element)) {
                return $class;
            }
        }

        return AssertableElement::class;
    }
}
