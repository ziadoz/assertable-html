<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Dom\Elements\AssertableForm;

final readonly class AssertableElementPromoter
{
    private const array CUSTOM_ELEMENTS = [
        AssertableForm::class,
    ];

    /** Create a core assertable element. */
    public function __construct(private HTMLElement|Element $element)
    {
    }

    /** Promote and return the first matching assertable element that matches the given HTML element. */
    public function promote(): AssertableElement
    {
        $match = array_find(
            self::CUSTOM_ELEMENTS,
            fn (string $customElement): bool => $customElement::shouldPromote($this->element),
        );

        return $match ? new $match($this->element) : new AssertableElement($this->element);
    }
}
