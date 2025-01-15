<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Dom\Document;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Support\Whitespace;

trait AssertsHtmlElement
{
    // @todo: Update to use new assertable element, list and text classes.

    /*
    |--------------------------------------------------------------------------
    | Assert Title
    |--------------------------------------------------------------------------
    */

    /** Assert the page title equals the given value. */
    public function assertTitleEquals(string $title, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $title,
            $this->element->ownerDocument->title,
            $message ?? "The page title doesn't equal the given title.",
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Element
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element passes the given callback.
     *
     * @param  callable(HtmlElement $element): bool  $callback
     */
    public function assertElement(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this->element),
            $message ?? sprintf(
                "The element [%s] doesn't pass the given callback.",
                $this->identifier(),
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Matches Selector
    |--------------------------------------------------------------------------
    */

    /** Assert the element matches the given selector. */
    public function assertMatchesSelector(string $selector, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $this->element->matches($selector),
            $message ?? sprintf(
                "The element [%s] doesn't match the given selector [%s].",
                $this->identifier(),
                $selector,
            ),
        );

        return $this;
    }

    /** Assert the element doesn't match the given selector. */
    public function assertDoesntMatchSelector(string $selector, ?string $message = null): static
    {
        PHPUnit::assertFalse(
            $this->element->matches($selector),
            $message ?? sprintf(
                'The element [%s] matches the given selector [%s].',
                $this->identifier(),
                $selector,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Count
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element the expected number of elements matching the given selector.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function assertNumberOfElements(string $selector, string $comparison, int $expected, ?string $message = null): static
    {
        if ($expected < 0) {
            throw new InvalidArgumentException('Expected count of elements cannot be less than zero');
        }

        $elements = $this->element->querySelectorAll($selector);

        $message ??= sprintf(
            "The element [%s] doesn't have %s [%d] elements matching the selector [%s].",
            $this->identifier(),
            match ($comparison) {
                '='     => 'exactly',
                '>'     => 'greater than',
                '>='    => 'greater than or equal to',
                '<'     => 'less than',
                '<='    => 'less than or equal to',
                default => throw new OutOfBoundsException('Invalid comparison operator: ' . $comparison),
            },
            $expected,
            $selector,
        );

        match ($comparison) {
            '='     => PHPUnit::assertCount($expected, $elements, $message),
            '>'     => PHPUnit::assertGreaterThan($expected, count($elements), $message),
            '>='    => PHPUnit::assertGreaterThanOrEqual($expected, count($elements), $message),
            '<'     => PHPUnit::assertLessThan($expected, count($elements), $message),
            '<='    => PHPUnit::assertLessThanOrEqual($expected, count($elements), $message),
            default => throw new OutOfBoundsException('Invalid comparison operator: ' . $comparison),
        };

        return $this;
    }

    /** Assert the element contains the exact number of elements matching the given selector. */
    public function assertElementsCount(string $selector, int $expected, ?string $message = null): static
    {
        $this->assertNumberOfElements($selector, '=', $expected, $message);

        return $this;
    }

    /** Assert the element contains greater than the number of elements matching the given selector. */
    public function assertElementsGreaterThan(string $selector, int $expected, ?string $message = null): static
    {
        $this->assertNumberOfElements($selector, '>', $expected, $message);

        return $this;
    }

    /** Assert the element contains greater than or equal the number of elements matching the given selector. */
    public function assertElementsGreaterThanOrEqual(string $selector, int $expected, ?string $message = null): static
    {
        $this->assertNumberOfElements($selector, '>=', $expected, $message);

        return $this;
    }

    /** Assert the element contains less than the number of elements matching the given selector. */
    public function assertElementsLessThan(string $selector, int $expected, ?string $message = null): static
    {
        $this->assertNumberOfElements($selector, '<', $expected, $message);

        return $this;
    }

    /** Assert the element contains less than or equal the number of elements matching the given selector. */
    public function assertElementsLessThanOrEqual(string $selector, int $expected, ?string $message = null): static
    {
        $this->assertNumberOfElements($selector, '<=', $expected, $message);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element's text passes the given callback.
     *
     * @param  callable(string $text): bool  $callback
     */
    public function assertText(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this->element->textContent),
            $message ?? sprintf(
                "The element [%s] text doesn't pass the given callback.",
                $this->identifier(),
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the element's text equals the given text. */
    public function assertTextEquals(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $text,
            $this->normaliseTextContent($this->element, $normaliseWhitespace),
            $message ?? sprintf(
                "The element [%s] text doesn't equal the given text.",
                $this->identifier(),
            ),
        );

        return $this;
    }

    /** Assert the element's text doesn't equal the given text. */
    public function assertTextDoesntEqual(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertNotSame(
            $text,
            $this->normaliseTextContent($this->element, $normaliseWhitespace),
            $message ?? sprintf(
                'The element [%s] text equals the given text.',
                $this->identifier(),
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the element's text contains the given text. */
    public function assertTextContains(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringContainsString(
            $text,
            $this->normaliseTextContent($this->element, $normaliseWhitespace),
            $message ?? sprintf(
                "The element [%s] text doesn't contain the given text.",
                $this->identifier(),
            ),
        );

        return $this;
    }

    /** Alias for assertTextContains() */
    public function assertSeeIn(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertTextContains($text, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the element's text doesn't contain the given text. */
    public function assertTextDoesntContain(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringNotContainsString(
            $text,
            $this->normaliseTextContent($this->element, $normaliseWhitespace),
            $message ?? sprintf(
                'The element [%s] text contains the given text.',
                $this->identifier(),
            ),
        );

        return $this;
    }

    /** Alias for assertTextDoesntContain() */
    public function assertDontSeeIn(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertTextDoesntContain($text, $normaliseWhitespace, $message);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element's class passes the given callback.
     *
     * @param  callable(array $classes): bool  $callback
     */
    public function assertClass(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback(iterator_to_array($this->element->classList)),
            $message ?? sprintf(
                "The element [%s] class doesn't pass the given callback.",
                $this->identifier(),
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Present/Missing
    |--------------------------------------------------------------------------
    */

    /** Assert the element has a class. */
    public function assertClassPresent(): static
    {
        $this->assertAttributePresent('class', sprintf(
            'The element [%s] is missing the class attribute.',
            $this->identifier(),
        ));

        return $this;
    }

    /** Assert the element is missing a class. */
    public function assertClassMissing(): static
    {
        $this->assertAttributeMissing('class', sprintf(
            'The element [%s] has the class attribute.',
            $this->identifier(),
        ));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class equals the given class. */
    public function assertClassEquals(string $class, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $class,
            $this->normaliseClasses($this->element, $normaliseWhitespace),
            $message ?? sprintf(
                "The element [%s] class doesn't equal the given class [%s].",
                $this->identifier(),
                $class,
            ),
        );

        return $this;
    }

    /** Assert the element's class doesn't equal the given class. */
    public function assertClassDoesntEqual(string $class, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertNotSame(
            $class,
            $this->normaliseClasses($this->element, $normaliseWhitespace),
            $message ?? sprintf(
                'The element [%s] class equals the given class [%s].',
                $this->identifier(),
                $class,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class contains the given class. */
    public function assertClassContains(string $class, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $this->element->classList->contains($class),
            $message ?? sprintf(
                "The element [%s] class doesn't contain the given class [%s].",
                $this->identifier(),
                $class,
            ),
        );

        return $this;
    }

    /** Assert the element's class doesn't contain the given class. */
    public function assertClassDoesntContain(string $class, ?string $message = null): static
    {
        PHPUnit::assertFalse(
            $this->element->classList->contains($class),
            $message ?? sprintf(
                'The element [%s] class contains the given class [%s].',
                $this->identifier(),
                $class,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains All
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class contains all the given classes. */
    public function assertClassContainsAll(array $classes, ?string $message = null): static
    {
        $classes = array_values($classes);

        PHPUnit::assertTrue(
            array_intersect(iterator_to_array($this->element->classList), $classes) === $classes,
            $message ?? sprintf(
                "The element [%s] class doesn't contain all the given classes [%s].",
                $this->identifier(),
                implode(' ', $classes),
            ),
        );

        return $this;
    }

    /** Assert the element's class doesn't contain all the given classes. */
    public function assertClassDoesntContainAll(array $classes, ?string $message = null): static
    {
        $classes = array_values($classes);

        PHPUnit::assertFalse(
            array_intersect(iterator_to_array($this->element->classList), $classes) === $classes,
            $message ?? sprintf(
                'The element [%s] class contains all the given classes [%s].',
                $this->identifier(),
                implode(' ', $classes),
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element's attribute passes the given callback.
     *
     * @param  callable(?string $value): bool  $callback
     */
    public function assertAttribute(string $attribute, callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this->element->getAttribute($attribute)),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't pass the given callback.",
                $this->identifier(),
                $attribute,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Present/Missing
    |--------------------------------------------------------------------------
    */

    /** Assert the element has the given attribute. */
    public function assertAttributePresent(string $attribute, ?string $message = null): static
    {
        PHPUnit::assertNotNull(
            $this->element->getAttribute($attribute),
            $message ?? sprintf(
                'The element [%s] is missing the given attribute [%s].',
                $this->identifier(),
                $attribute,
            ),
        );

        return $this;
    }

    /** Assert the element is missing the given attribute. */
    public function assertAttributeMissing(string $attribute, ?string $message = null): static
    {
        PHPUnit::assertNull(
            $this->element->getAttribute($attribute),
            $message ?? sprintf(
                'The element [%s] has the given attribute [%s].',
                $this->identifier(),
                $attribute,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the given element's attribute equals the given value. */
    public function assertAttributeEquals(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $value,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't equal the given value [%s].",
                $this->identifier(),
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertNotSame(
            $value,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The element [%s] attribute [%s] equals the given value [%s].',
                $this->identifier(),
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the given element's attribute contains the given value. */
    public function assertAttributeContains(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringContainsString(
            $value,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't contain the given value [%s].",
                $this->identifier(),
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /** Assert the given element's class doesn't contain the given value. */
    public function assertAttributeDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringNotContainsString(
            $value,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The element [%s] attribute [%s] contains the given value [%s].',
                $this->identifier(),
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Starts / Ends With
    |--------------------------------------------------------------------------
    */

    /** Assert the attribute starts with the given prefix. */
    public function assertAttributeStartsWith(string $attribute, string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringStartsWith(
            $prefix,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't start with the given prefix [%s].",
                $this->identifier(),
                $attribute,
                $prefix,
            ),
        );

        return $this;
    }

    /** Assert the attribute doesn't start with the given prefix. */
    public function assertAttributeDoesntStartWith(string $attribute, string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringStartsNotWith(
            $prefix,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The element [%s] attribute [%s] starts with the given prefix [%s].',
                $this->identifier(),
                $attribute,
                $prefix,
            ),
        );

        return $this;
    }

    /** Assert the attribute ends with the given prefix. */
    public function assertAttributeEndsWith(string $attribute, string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringEndsWith(
            $suffix,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't end with the given suffix [%s].",
                $this->identifier(),
                $attribute,
                $suffix,
            ),
        );

        return $this;
    }

    /** Assert the attribute doesn't start with the given prefix. */
    public function assertAttributeDoesntEndWith(string $attribute, string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringEndsNotWith(
            $suffix,
            $this->normaliseAttribute($this->element, $attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The element [%s] attribute [%s] ends with the given suffix [%s].',
                $this->identifier(),
                $attribute,
                $suffix,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Is Allowed
    |--------------------------------------------------------------------------
    */

    /** Assert the given element's attribute is the allowed value. */
    public function assertAttributeIsAllowed(string $attribute, string $value, ?string $message = null): static
    {
        PHPUnit::assertSame(
            strtolower(trim($value)),
            strtolower(trim((string) $this->element->getAttribute($attribute))),
            $message ?? sprintf(
                "The element [%s] attribute [%s] isn't the allowed value [%s].",
                $this->identifier(),
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Data Attribute
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element's data attribute passes the given callback.
     *
     * @param  callable(string|null $value): bool  $callback
     */
    public function assertDataAttribute(string $attribute, callable $callback, ?string $message = null): static
    {
        $this->assertAttribute($this->prefixDataAttribute($attribute), $callback, $message);

        return $this;
    }

    /** Assert the element has the given data attribute. */
    public function assertDataAttributePresent(string $attribute, ?string $message = null): static
    {
        $this->assertAttributePresent($this->prefixDataAttribute($attribute), $message);

        return $this;
    }

    /** Assert the element is missing the given data attribute. */
    public function assertDataAttributeMissing(string $attribute, ?string $message = null): static
    {
        $this->assertAttributeMissing($this->prefixDataAttribute($attribute), $message);

        return $this;
    }

    /** Assert the given element's data attribute equals the given value. */
    public function assertDataAttributeEquals(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeEquals($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertDataAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeDoesntEqual($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the given element's data attribute contains the given value. */
    public function assertDataAttributeContains(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeContains($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the given element's data attribute doesn't contain the given value. */
    public function assertDataAttributeDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeDoesntContain($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Aria Attribute
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element's data attribute passes the given callback.
     *
     * @param  callable(string|null $value): bool  $callback
     */
    public function assertAriaAttribute(string $attribute, callable $callback, ?string $message = null): static
    {
        $this->assertAttribute($this->prefixAriaAttribute($attribute), $callback, $message);

        return $this;
    }

    /** Assert the element has the given data attribute. */
    public function assertAriaAttributePresent(string $attribute, ?string $message = null): static
    {
        $this->assertAttributePresent($this->prefixAriaAttribute($attribute), $message);

        return $this;
    }

    /** Assert the element is missing the given data attribute. */
    public function assertAriaAttributeMissing(string $attribute, ?string $message = null): static
    {
        $this->assertAttributeMissing($this->prefixAriaAttribute($attribute), $message);

        return $this;
    }

    /** Assert the given element's data attribute equals the given value. */
    public function assertAriaAttributeEquals(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeEquals($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertAriaAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeDoesntEqual($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the given element's data attribute contains the given value. */
    public function assertAriaAttributeContains(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeContains($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the given element's data attribute doesn't contain the given value. */
    public function assertAriaAttributeDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertAttributeDoesntContain($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Internal
    |--------------------------------------------------------------------------
    */

    /** Prefix the given attribute name with "data-" if applicable. */
    protected function prefixDataAttribute(string $attribute): string
    {
        return (! str_starts_with($attribute, 'data-') ? 'data-' : '') . $attribute;
    }

    /** Prefix the given attribute name with "aria-" if applicable. */
    protected function prefixAriaAttribute(string $attribute): string
    {
        return (! str_starts_with($attribute, 'aria-') ? 'aria-' : '') . $attribute;
    }

    /** Normalise the given element's text content. */
    protected function normaliseTextContent(HTMLDocument|Document|HTMLElement|Element $element, bool $normaliseWhitespace = false): string
    {
        return $normaliseWhitespace
            ? Whitespace::normalise($element->textContent)
            : $element->textContent;
    }

    /** Normalise the given element's classes. */
    protected function normaliseClasses(HTMLDocument|Document|HTMLElement|Element $element, bool $normaliseWhitespace = false): string
    {
        return $normaliseWhitespace
            ? implode(' ', iterator_to_array($element->classList))
            : $element->classList->value;
    }

    /** Normalise the given element's attribute. */
    protected function normaliseAttribute(HTMLDocument|Document|HTMLElement|Element $element, string $attribute, bool $normaliseWhitespace = false): string
    {
        return $normaliseWhitespace
            ? Whitespace::normalise((string) $element->getAttribute($attribute))
            : (string) $this->element->getAttribute($attribute);
    }
}
