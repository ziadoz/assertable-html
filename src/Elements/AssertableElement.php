<?php

namespace Ziadoz\AssertableHtml\Elements;

use Dom\Document;
use Dom\Element;
use Dom\HtmlElement;
use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Support\Utilities;

class AssertableElement implements AssertableElementInterface
{
    /** Create an assertable HTML element. */
    public function __construct(protected HtmlElement|Element $root)
    {
    }

    /** Return the underlying HTML document instance. */
    public function getDocument(): Document
    {
        return $this->root->ownerDocument;
    }

    /** Return the root HTML element assertions are being performed on. */
    public function getRoot(): HtmlElement
    {
        return $this->root;
    }

    /** Return the root element HTML. */
    public function getHtml(): string
    {
        return $this->root->ownerDocument->saveHtml($this->root);
    }

    /** Dump the root element HTML. */
    public function dump(): void
    {
        dump($this->getHtml());
    }

    /** Dump and die the root element HTML. */
    public function dd(): never
    {
        dd($this->getHtml());
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
    public function assertElement(callable $callback, ?string $message = null): void
    {
        PHPUnit::assertTrue(
            $callback($this->root),
            $message ?? sprintf(
                "The element [%s] doesn't pass the given callback.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Matches Selector
    |--------------------------------------------------------------------------
    */

    /** Assert the element matches the given selector. */
    public function assertMatchesSelector(string $selector, ?string $message = null): void
    {
        PHPUnit::assertTrue(
            $this->root->matches($selector),
            $message ?? sprintf(
                "The element [%s] doesn't match the given selector [%s].",
                Utilities::selectorFromElement($this->root),
                $selector,
            ),
        );
    }

    /** Assert the element doesn't match the given selector. */
    public function assertDoesntMatchSelector(string $selector, ?string $message = null): void
    {
        PHPUnit::assertFalse(
            $this->root->matches($selector),
            $message ?? sprintf(
                'The element [%s] matches the given selector [%s].',
                Utilities::selectorFromElement($this->root),
                $selector,
            ),
        );
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
    public function assertNumberOfElements(string $selector, string $comparison, int $expected, ?string $message = null): void
    {
        if ($expected < 0) {
            throw new InvalidArgumentException('Expected count of elements cannot be less than zero');
        }

        $elements = $this->root->querySelectorAll($selector);

        $message ??= sprintf(
            "The element [%s] doesn't have %s [%d] elements matching the selector [%s].",
            Utilities::selectorFromElement($this->root),
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
    }

    /** Assert the element contains the exact number of elements matching the given selector. */
    public function assertElementsCount(string $selector, int $expected, ?string $message = null): void
    {
        $this->assertNumberOfElements($selector, '=', $expected, $message);
    }

    /** Assert the element contains greater than the number of elements matching the given selector. */
    public function assertElementsGreaterThan(string $selector, int $expected, ?string $message = null): void
    {
        $this->assertNumberOfElements($selector, '>', $expected, $message);
    }

    /** Assert the element contains greater than or equal the number of elements matching the given selector. */
    public function assertElementsGreaterThanOrEqual(string $selector, int $expected, ?string $message = null): void
    {
        $this->assertNumberOfElements($selector, '>=', $expected, $message);
    }

    /** Assert the element contains less than the number of elements matching the given selector. */
    public function assertElementsLessThan(string $selector, int $expected, ?string $message = null): void
    {
        $this->assertNumberOfElements($selector, '<', $expected, $message);
    }

    /** Assert the element contains less than or equal the number of elements matching the given selector. */
    public function assertElementsLessThanOrEqual(string $selector, int $expected, ?string $message = null): void
    {
        $this->assertNumberOfElements($selector, '<=', $expected, $message);
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
    public function assertText(callable $callback, ?string $message = null): void
    {
        PHPUnit::assertTrue(
            $callback($this->root->textContent),
            $message ?? sprintf(
                "The element [%s] text doesn't pass the given callback.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the element's text equals the given text. */
    public function assertTextEquals(string $text, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertSame(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            $message ?? sprintf(
                "The element [%s] text doesn't equal the given text.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Assert the element's text doesn't equal the given text. */
    public function assertTextDoesntEqual(string $text, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertNotSame(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            $message ?? sprintf(
                'The element [%s] text equals the given text.',
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the element's text contains the given text. */
    public function assertTextContains(string $text, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertStringContainsString(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            $message ?? sprintf(
                "The element [%s] text doesn't contain the given text.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Alias for assertTextContains() */
    public function assertSeeIn(string $text, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertTextContains($text, $normaliseWhitespace, $message);
    }

    /** Assert the element's text doesn't contain the given text. */
    public function assertTextDoesntContain(string $text, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertStringNotContainsString(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            $message ?? sprintf(
                'The element [%s] text contains the given text.',
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Alias for assertTextDoesntContain() */
    public function assertDontSeeIn(string $text, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertTextDoesntContain($text, $normaliseWhitespace, $message);
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
    public function assertClass(callable $callback, ?string $message = null): void
    {
        PHPUnit::assertTrue(
            $callback(iterator_to_array($this->root->classList)),
            $message ?? sprintf(
                "The element [%s] class doesn't pass the given callback.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Present/Missing
    |--------------------------------------------------------------------------
    */

    /** Assert the element has a class. */
    public function assertClassPresent(): void
    {
        $this->assertAttributePresent('class', sprintf(
            'The element [%s] is missing the class attribute.',
            Utilities::selectorFromElement($this->root),
        ));
    }

    /** Assert the element is missing a class. */
    public function assertClassMissing(): void
    {
        $this->assertAttributeMissing('class', sprintf(
            'The element [%s] has the class attribute.',
            Utilities::selectorFromElement($this->root),
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class equals the given class. */
    public function assertClassEquals(string $class, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertSame(
            $class,
            $normaliseWhitespace
                ? implode(' ', iterator_to_array($this->root->classList))
                : $this->root->classList->value,
            $message ?? sprintf(
                "The element [%s] class doesn't equal the given class [%s].",
                Utilities::selectorFromElement($this->root),
                $class,
            ),
        );
    }

    /** Assert the element's class doesn't equal the given class. */
    public function assertClassDoesntEqual(string $class, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertNotSame(
            $class,
            $normaliseWhitespace
                ? implode(' ', iterator_to_array($this->root->classList))
                : $this->root->classList->value,
            $message ?? sprintf(
                'The element [%s] class equals the given class [%s].',
                Utilities::selectorFromElement($this->root),
                $class,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class contains the given class. */
    public function assertClassContains(string $class, ?string $message = null): void
    {
        PHPUnit::assertTrue(
            $this->root->classList->contains($class),
            $message ?? sprintf(
                "The element [%s] class doesn't contain the given class [%s].",
                Utilities::selectorFromElement($this->root),
                $class,
            ),
        );
    }

    /** Assert the element's class doesn't contain the given class. */
    public function assertClassDoesntContain(string $class, ?string $message = null): void
    {
        PHPUnit::assertFalse(
            $this->root->classList->contains($class),
            $message ?? sprintf(
                'The element [%s] class contains the given class [%s].',
                Utilities::selectorFromElement($this->root),
                $class,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains All
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class contains all the given classes. */
    public function assertClassContainsAll(array $classes, ?string $message = null): void
    {
        $classes = array_values($classes);

        PHPUnit::assertTrue(
            array_intersect(iterator_to_array($this->root->classList), $classes) === $classes,
            $message ?? sprintf(
                "The element [%s] class doesn't contain all the given classes [%s].",
                Utilities::selectorFromElement($this->root),
                implode(' ', $classes),
            ),
        );
    }

    /** Assert the element's class doesn't contain all the given classes. */
    public function assertClassDoesntContainAll(array $classes, ?string $message = null): void
    {
        $classes = array_values($classes);

        PHPUnit::assertFalse(
            array_intersect(iterator_to_array($this->root->classList), $classes) === $classes,
            $message ?? sprintf(
                'The element [%s] class contains all the given classes [%s].',
                Utilities::selectorFromElement($this->root),
                implode(' ', $classes),
            ),
        );
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
    public function assertAttribute(string $attribute, callable $callback, ?string $message = null): void
    {
        PHPUnit::assertTrue(
            $callback($this->root->getAttribute($attribute)),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't pass the given callback.",
                Utilities::selectorFromElement($this->root),
                $attribute,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Present/Missing
    |--------------------------------------------------------------------------
    */

    /** Assert the element has the given attribute. */
    public function assertAttributePresent(string $attribute, ?string $message = null): void
    {
        PHPUnit::assertNotNull(
            $this->root->getAttribute($attribute),
            $message ?? sprintf(
                'The element [%s] is missing the given attribute [%s].',
                Utilities::selectorFromElement($this->root),
                $attribute,
            ),
        );
    }

    /** Assert the element is missing the given attribute. */
    public function assertAttributeMissing(string $attribute, ?string $message = null): void
    {
        PHPUnit::assertNull(
            $this->root->getAttribute($attribute),
            $message ?? sprintf(
                'The element [%s] has the given attribute [%s].',
                Utilities::selectorFromElement($this->root),
                $attribute,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the given element's attribute equals the given value. */
    public function assertAttributeEquals(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertSame(
            $value,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace((string) $this->root->getAttribute($attribute))
                : (string) $this->root->getAttribute($attribute),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't equal the given value [%s].",
                Utilities::selectorFromElement($this->root),
                $attribute,
                $value,
            ),
        );
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertNotSame(
            $value,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace((string) $this->root->getAttribute($attribute))
                : (string) $this->root->getAttribute($attribute),
            $message ?? sprintf(
                'The element [%s] attribute [%s] equals the given value [%s].',
                Utilities::selectorFromElement($this->root),
                $attribute,
                $value,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the given element's attribute contains the given value. */
    public function assertAttributeContains(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertStringContainsString(
            $value,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace((string) $this->root->getAttribute($attribute))
                : (string) $this->root->getAttribute($attribute),
            $message ?? sprintf(
                "The element [%s] attribute [%s] doesn't contain the given value [%s].",
                Utilities::selectorFromElement($this->root),
                $attribute,
                $value,
            ),
        );
    }

    /** Assert the given element's class doesn't contain the given value. */
    public function assertAttributeDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        PHPUnit::assertStringNotContainsString(
            $value,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace((string) $this->root->getAttribute($attribute))
                : (string) $this->root->getAttribute($attribute),
            $message ?? sprintf(
                'The element [%s] attribute [%s] contains the given value [%s].',
                Utilities::selectorFromElement($this->root),
                $attribute,
                $value,
            ),
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Data Attribute
    |--------------------------------------------------------------------------
    */

    /** Prefix the given attribute name with "data-" if applicable. */
    protected function prefixDataAttribute(string $attribute): string
    {
        return (! str_starts_with($attribute, 'data-') ? 'data-' : '') . $attribute;
    }

    /**
     * Assert the element's data attribute passes the given callback.
     *
     * @param  callable(string|null $value): bool  $callback
     */
    public function assertDataAttribute(string $attribute, callable $callback, ?string $message = null): void
    {
        $this->assertAttribute($this->prefixDataAttribute($attribute), $callback, $message);
    }

    /** Assert the element has the given data attribute. */
    public function assertDataAttributePresent(string $attribute, ?string $message = null): void
    {
        $this->assertAttributePresent($this->prefixDataAttribute($attribute), $message);
    }

    /** Assert the element is missing the given data attribute. */
    public function assertDataAttributeMissing(string $attribute, ?string $message = null): void
    {
        $this->assertAttributeMissing($this->prefixDataAttribute($attribute), $message);
    }

    /** Assert the given element's data attribute equals the given value. */
    public function assertDataAttributeEquals(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeEquals($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertDataAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeDoesntEqual($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);
    }

    /** Assert the given element's data attribute contains the given value. */
    public function assertDataAttributeContains(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeContains($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);
    }

    /** Assert the given element's data attribute doesn't contain the given value. */
    public function assertDataAttributeDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeDoesntContain($this->prefixDataAttribute($attribute), $value, $normaliseWhitespace, $message);
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Aria Attribute
    |--------------------------------------------------------------------------
    */

    /** Prefix the given attribute name with "aria-" if applicable. */
    protected function prefixAriaAttribute(string $attribute): string
    {
        return (! str_starts_with($attribute, 'aria-') ? 'aria-' : '') . $attribute;
    }

    /**
     * Assert the element's data attribute passes the given callback.
     *
     * @param  callable(string|null $value): bool  $callback
     */
    public function assertAriaAttribute(string $attribute, callable $callback, ?string $message = null): void
    {
        $this->assertAttribute($this->prefixAriaAttribute($attribute), $callback, $message);
    }

    /** Assert the element has the given data attribute. */
    public function assertAriaAttributePresent(string $attribute, ?string $message = null): void
    {
        $this->assertAttributePresent($this->prefixAriaAttribute($attribute), $message);
    }

    /** Assert the element is missing the given data attribute. */
    public function assertAriaAttributeMissing(string $attribute, ?string $message = null): void
    {
        $this->assertAttributeMissing($this->prefixAriaAttribute($attribute), $message);
    }

    /** Assert the given element's data attribute equals the given value. */
    public function assertAriaAttributeEquals(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeEquals($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertAriaAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeDoesntEqual($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);
    }

    /** Assert the given element's data attribute contains the given value. */
    public function assertAriaAttributeContains(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeContains($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);
    }

    /** Assert the given element's data attribute doesn't contain the given value. */
    public function assertAriaAttributeDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): void
    {
        $this->assertAttributeDoesntContain($this->prefixAriaAttribute($attribute), $value, $normaliseWhitespace, $message);
    }
}
