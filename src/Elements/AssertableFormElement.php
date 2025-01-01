<?php

namespace Ziadoz\AssertableHtml\Elements;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Contracts\MatchableInterface;
use Ziadoz\AssertableHtml\Support\Utilities;

class AssertableFormElement extends AssertableElement implements MatchableInterface
{
    /** Return if the given element matches. */
    public static function matches(HTMLElement|Element $element): bool
    {
        return $element->tagName === 'FORM';
    }

    /** Assert the form's method is GET. */
    public function assertMethodGet(): static
    {
        $this->assertAttribute(
            'method',
            fn (string $value): bool => strtolower(trim($value)) === 'get',
            sprintf(
                "The element [%s] attribute [method] doesn't equal GET.",
                Utilities::selectorFromElement($this->root),
            ),
        );

        return $this;
    }

    /** Assert the form's method is POST. */
    public function assertMethodPost(): static
    {
        $this->assertAttribute(
            'method',
            fn (string $value): bool => strtolower(trim($value)) === 'post',
            sprintf(
                "The element [%s] attribute [method] doesn't equal POST.",
                Utilities::selectorFromElement($this->root),
            ),
        );

        return $this;
    }

    /** Assert the form's action equals the given value. */
    public function assertActionEquals(string $action): static
    {
        $this->assertAttributeEquals('action', $action);

        return $this;
    }

    /** Assert the form's accepts uploads. */
    public function assertAcceptsUploads(): static
    {
        $this->assertAttribute(
            'enctype',
            fn (string $value): bool => strtolower(trim($value)) === 'multipart/form-data',
            sprintf(
                "The element [%s] attribute [enctype] doesn't equal multipart/form-data.",
                Utilities::selectorFromElement($this->root),
            ),
        );

        return $this;
    }
}
