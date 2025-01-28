<?php

namespace Ziadoz\AssertableHtml\Assertions;

use Ziadoz\AssertableHtml\Dom\AssertableElement;

final readonly class Short
{
    // @todo: Handle not() / and() / or()???
    // @todo: Some kind of higher order proxy???

    public function __construct(private AssertableElement $assertable)
    {
    }

    public function element(callable $callback): self
    {
        $this->assertable->assertElement($callback);

        return $this;
    }

    public function tag(string $tag): self
    {
        $this->assertable->assertTagEquals($tag);

        return $this;
    }

    public function text(string|callable $value): self
    {
        if (is_string($value)) {
            $this->assertable->assertTextContains($value, true);
        } else {
            $this->assertable->assertText($value);
        }

        return $this;
    }

    public function attrs(array|callable $value): self
    {
        if (is_array($value)) {
            $this->assertable->assertAttributesEqualArray($value, true);
        } else {
            $this->assertable->assertAttributes($value);
        }

        return $this;
    }

    public function attr(string $attribute, string|callable $value): self
    {
        if (is_string($value)) {
            $this->assertable->assertAttributeContains($attribute, $value);
        } else {
            $this->assertable->assertAttribute($attribute, $value);
        }

        return $this;
    }

    public function id(string|callable $value): self
    {
        if (is_string($value)) {
            $this->assertable->assertIdEquals($value);
        } else {
            $this->assertable->assertAttribute('id', $value);
        }

        return $this;
    }

    public function class(string|callable $value): self
    {
        if (is_string($value)) {
            $this->assertable->assertClassContains($value);
        } else {
            $this->assertable->assertAttribute('class', $value);
        }

        return $this;
    }

    public function matches(string $selector): self
    {
        $this->assertable->assertMatchesSelector($selector);

        return $this;
    }
}
