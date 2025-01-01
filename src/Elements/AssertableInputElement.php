<?php

namespace Ziadoz\AssertableHtml\Elements;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Contracts\MatchableInterface;
use Ziadoz\AssertableHtml\Support\Utilities;

class AssertableInputElement extends AssertableElement implements MatchableInterface
{
    /** Return if the given element matches. */
    public static function matches(HTMLElement|Element $element): bool
    {
        return $element->tagName === 'INPUT';
    }

    /** Assert the input is of the given type. */
    public function assertType(string $type): static
    {
        $this->assertAttributeIsAllowed('type', $type);

        return $this;
    }

    /** Assert the input name equals the given value. */
    public function assertNameEquals(string $name): static
    {
        $this->assertAttributeEquals('name', $name);

        return $this;
    }

    /** Assert the input name starts with the given value. */
    public function assertNameStartsWith(string $prefix): static
    {
        $this->assertAttributeStartsWith('name', $prefix);

        return $this;
    }

    /** Assert the input value equals the given value. */
    public function assertValueEquals(string $value): static
    {
        $this->assertAttributeEquals('value', $value);

        return $this;
    }

    /** Assert the input value doesn't equal the given value. */
    public function assertValueDoesntEqual(string $value): static
    {
        $this->assertAttributeDoesntEqual('value', $value);

        return $this;
    }

    /** Assert the input value contains the given value. */
    public function assertValueContains(string $value): static
    {
        $this->assertAttributeContains('value', $value);

        return $this;
    }

    /** Assert the input value doesn't contain the given value. */
    public function assertValueDoesntContain(string $value): static
    {
        $this->assertAttributeDoesntContain('value', $value);

        return $this;
    }

    /** Check the given input is checked. */
    public function assertChecked(): static
    {
        $this->assertAttributePresent(
            'checked',
            sprintf(
                "The element [%s] isn't checked.",
                Utilities::selectorFromElement($this->root),
            ),
        );

        return $this;
    }

    /** Check the given input is unchecked. */
    public function assertUnchecked(): static
    {
        $this->assertAttributeMissing(
            'checked',
            sprintf(
                "The element [%s] isn't unchecked.",
                Utilities::selectorFromElement($this->root),
            ),
        );

        return $this;
    }
}
