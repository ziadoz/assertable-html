<?php

namespace Ziadoz\AssertableHtml\Elements;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Contracts\AssertableElementInterface;
use Ziadoz\AssertableHtml\Contracts\MatchableInterface;
use Ziadoz\AssertableHtml\Support\Utilities;

class AssertableFormElement extends AssertableElement implements AssertableElementInterface, MatchableInterface
{
    /** Assert the form's method is GET. */
    public function assertMethodGet(): void
    {
        $this->assertAttribute(
            'method',
            fn (string $value): bool => strtolower($value) === 'get',
            sprintf(
                "The element [%s] attribute [method] doesn't equal GET.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Assert the form's method is POST. */
    public function assertMethodPost(): void
    {
        $this->assertAttribute(
            'method',
            fn (string $value): bool => strtolower($value) === 'post',
            sprintf(
                "The element [%s] attribute [method] doesn't equal POST.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Assert the form's accepts uploads. */
    public function assertAcceptsUploads(): void
    {
        $this->assertAttribute(
            'enctype',
            fn (string $value): bool => strtolower($value) === 'multipart/form-data',
            sprintf(
                "The element [%s] attribute [enctype] doesn't equal multipart/form-data.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Return if the given element matches. */
    public static function matches(Element|HTMLElement $element): bool
    {
        return $element->tagName === 'FORM';
    }
}
