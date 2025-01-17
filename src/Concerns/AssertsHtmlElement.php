<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Dom\AssertableAttributesList;
use Ziadoz\AssertableHtml\Dom\AssertableClassList;
use Ziadoz\AssertableHtml\Dom\AssertableHtmlElement;
use Ziadoz\AssertableHtml\Dom\AssertableText;

trait AssertsHtmlElement
{
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
    | Assert Tag
    |--------------------------------------------------------------------------
    */

    /** Assert the element's tag matches the given tag. */
    public function assertTag(string $tag, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $expected = strtolower($tag),
            $actual = strtolower($this->element->tagName),
            $message ?? sprintf(
                "The element [%s] tag name [%s] doesn't match the given tag [%s].",
                $this->identifier(),
                $expected,
                $actual,
            ),
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
     * @param  callable(AssertableHtmlElement $element): bool  $callback
     */
    public function assertElement(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this),
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
    public function assertNumberOfElements(string $selector, string $comparison, int $count, ?string $message = null): static
    {
        $this->querySelectorAll($selector)->assertNumberOfElements(
            $comparison,
            $count,
            sprintf(
                $message ?? "The element [%s] doesn't have %s [%d] elements matching the selector [%s].",
                $this->identifier(),
                match ($comparison) {
                    '='     => 'exactly',
                    '>'     => 'greater than',
                    '>='    => 'greater than or equal to',
                    '<'     => 'less than',
                    '<='    => 'less than or equal to',
                    default => throw new OutOfBoundsException('Invalid comparison operator: ' . $comparison),
                },
                $count,
                $selector,
            ),
        );

        return $this;
    }

    /** Assert the element contains the exact number of elements matching the given selector. */
    public function assertElementsCount(string $selector, int $count, ?string $message = null): static
    {
        $this->querySelectorAll($selector)->assertNumberOfElements('=', $count, $message);

        return $this;
    }

    /** Assert the element contains greater than the number of elements matching the given selector. */
    public function assertElementsCountGreaterThan(string $selector, int $count, ?string $message = null): static
    {
        $this->querySelectorAll($selector)->assertNumberOfElements('>', $count, $message);

        return $this;
    }

    /** Assert the element contains greater than or equal the number of elements matching the given selector. */
    public function assertElementsCountGreaterThanOrEqual(string $selector, int $count, ?string $message = null): static
    {
        $this->querySelectorAll($selector)->assertNumberOfElements('>=', $count, $message);

        return $this;
    }

    /** Assert the element contains less than the number of elements matching the given selector. */
    public function assertElementsCountLessThan(string $selector, int $count, ?string $message = null): static
    {
        $this->querySelectorAll($selector)->assertNumberOfElements('<', $count, $message);

        return $this;
    }

    /** Assert the element contains less than or equal the number of elements matching the given selector. */
    public function assertElementsCountLessThanOrEqual(string $selector, int $count, ?string $message = null): static
    {
        $this->querySelectorAll($selector)->assertNumberOfElements('<=', $count, $message);

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
     * @param  callable(AssertableText $text): bool  $callback
     */
    public function assertText(callable $callback, ?string $message = null): static
    {
        $this->text->assertText($callback, $message ?? sprintf(
            "The element [%s] text doesn't pass the given callback.",
            $this->identifier(),
        ));

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
        $this->text->assertEquals($text, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] text doesn't pass the given callback.",
            $this->identifier(),
        ));

        return $this;
    }

    /** Assert the element's text doesn't equal the given text. */
    public function assertTextDoesntEqual(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->text->assertDoesntEqual($text, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] text equals the given text.',
            $this->identifier(),
        ));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Starts / Ends With
    |--------------------------------------------------------------------------
    */

    /** Assert the element's text starts with the given text. */
    public function assertTextStartsWith(string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->text->assertStartsWith($prefix, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] text doesn't start with the given prefix.",
            $this->identifier(),
        ));

        return $this;
    }

    /** Assert the element's text starts doesn't start with the given text. */
    public function assertTextDoesntStartWith(string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->text->assertDoesntStartWith($prefix, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] text starts with the given prefix.',
            $this->identifier(),
        ));

        return $this;
    }

    /** Assert the element's text ends with the given text. */
    public function assertTextEndsWith(string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->text->assertEndsWith($suffix, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] text doesn't end with the given suffix.",
            $this->identifier(),
        ));

        return $this;
    }

    /** Assert the element's text doesn't end with the given text. */
    public function assertTextDoesntEndWith(string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->text->assertDoesntEndWith($suffix, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] text ends with the given suffix.',
            $this->identifier(),
        ));

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
        $this->text->assertContains($text, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] text doesn't contain the given text.",
            $this->identifier(),
        ));

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
        $this->text->assertDoesntContain($text, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] text contains the given text.',
            $this->identifier(),
        ));

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
    | Assert ID Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the element's ID equals the given value. */
    public function assertIdEquals(string $value, ?string $message = null): static
    {
        $this->attributes->assertEquals('id', $value, false, $message ?? sprintf(
            "The element [%s] doesn't equal the given value [%s].",
            $this->identifier(),
            $value,
        ));

        return $this;
    }

    /** Assert the element's ID doesn't equal the given value. */
    public function assertIdDoesntEqual(string $value, ?string $message = null): static
    {
        $this->attributes->assertDoesntEqual('id', $value, false, $message ?? sprintf(
            'The element [%s] equals the given value [%s].',
            $this->identifier(),
            $value,
        ));

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
     * @param  callable(AssertableClassList $classes): bool  $callback
     */
    public function assertClass(callable $callback, ?string $message = null): static
    {
        $this->classes->assertClasses($callback, $message ?? sprintf(
            "The element [%s] class doesn't pass the given callback.",
            $this->identifier(),
        ));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Classes Empty
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class list is empty. */
    public function assertClassesEmpty(?string $message = null): static
    {
        $this->classes->assertEmpty($message ?? "The class list isn't empty.");

        return $this;
    }

    /** Assert the element's class list isn't empty. */
    public function assertClassesNotEmpty(?string $message = null): static
    {
        $this->classes->assertNotEmpty($message ?? 'The class list is empty.');

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
        $this->attributes->assertPresent('class', sprintf(
            'The element [%s] is missing the class attribute.',
            $this->identifier(),
        ));

        return $this;
    }

    /** Assert the element is missing a class. */
    public function assertClassMissing(): static
    {
        $this->attributes->assertMissing('class', sprintf(
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
        $this->classes->assertValueEquals($class, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] class doesn't equal the given class [%s].",
            $this->identifier(),
            $class,
        ));

        return $this;
    }

    /** Assert the element's class doesn't equal the given class. */
    public function assertClassDoesntEqual(string $class, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->classes->assertValueDoesntEqual($class, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] class equals the given class [%s].',
            $this->identifier(),
            $class,
        ));

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
        $this->classes->assertContains($class, $message ?? sprintf(
            "The element [%s] class doesn't contain the given class [%s].",
            $this->identifier(),
            $class,
        ));

        return $this;
    }

    /** Assert the element's class doesn't contain the given class. */
    public function assertClassDoesntContain(string $class, ?string $message = null): static
    {
        $this->classes->assertDoesntContain($class, $message ?? sprintf(
            'The element [%s] class contains the given class [%s].',
            $this->identifier(),
            $class,
        ));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains All
    |--------------------------------------------------------------------------
    */

    /** Assert the element's class contains any of the given classes. */
    public function assertClassContainsAny(array $classes, ?string $message = null): static
    {
        $this->classes->assertContainsAny($classes, $message ?? sprintf(
            "The element [%s] class doesn't contain any of the given classes [%s].",
            $this->identifier(),
            implode(' ', $classes),
        ));

        return $this;
    }

    /** Assert the element's class doesn't contain any of the given classes. */
    public function assertClassDoesntContainAny(array $classes, ?string $message = null): static
    {
        $this->classes->assertDoesntContainAny($classes, $message ?? sprintf(
            'The element [%s] class contains some of the given classes [%s].',
            $this->identifier(),
            implode(' ', $classes),
        ));

        return $this;
    }

    /** Assert the element's class contains all the given classes. */
    public function assertClassContainsAll(array $classes, ?string $message = null): static
    {
        $this->classes->assertContainsAll($classes, $message ?? sprintf(
            "The element [%s] class doesn't contain all the given classes [%s].",
            $this->identifier(),
            implode(' ', $classes),
        ));

        return $this;
    }

    /** Assert the element's class doesn't contain all the given classes. */
    public function assertClassDoesntContainAll(array $classes, ?string $message = null): static
    {
        $this->classes->assertDoesntContainAll($classes, $message ?? sprintf(
            'The element [%s] class contains all the given classes [%s].',
            $this->identifier(),
            implode(' ', $classes),
        ));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element's attributes pass the given callback.
     *
     * @param  callable(AssertableAttributesList $value): bool  $callback
     */
    public function assertAttributes(callable $callback, ?string $message = null): static
    {
        $this->attributes->assertAttributes($callback, $message ?? sprintf(
            "The element [%s] attributes don't pass the given callback.",
            $this->identifier(),
        ));

        return $this;
    }

    /**
     * Assert the element's attribute passes the given callback.
     *
     * @param  callable(?string $value): bool  $callback
     */
    public function assertAttribute(string $attribute, callable $callback, ?string $message = null): static
    {
        $this->attributes->assertAttribute($attribute, $callback, $message ?? sprintf(
            "The element [%s] attribute [%s] doesn't pass the given callback.",
            $this->identifier(),
            $attribute,
        ));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Array
    |--------------------------------------------------------------------------
    */

    /** Assert the given associative array of attributes equals the attribute list. */
    public function assertAttributesEqualArray(array $attributes, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->attributes->assertEqualsArray($attributes, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] attributes doesn't equal the given array.",
            $this->identifier(),
        ));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attributes Empty
    |--------------------------------------------------------------------------
    */

    /** Assert the element's attributes list is empty. */
    public function assertAttributesEmpty(?string $message = null): static
    {
        $this->attributes->assertEmpty($message ?? "The attribute list isn't empty.");

        return $this;
    }

    /** Assert the element's attribute list isn't empty. */
    public function assertAttributesNotEmpty(?string $message = null): static
    {
        $this->attributes->assertNotEmpty($message ?? 'The attribute list is empty.');

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
        $this->attributes->assertPresent($attribute, $message ?? sprintf(
            'The element [%s] is missing the given attribute [%s].',
            $this->identifier(),
            $attribute,
        ));

        return $this;
    }

    /** Assert the element is missing the given attribute. */
    public function assertAttributeMissing(string $attribute, ?string $message = null): static
    {
        $this->attributes->assertMissing($attribute, $message ?? sprintf(
            'The element [%s] has the given attribute [%s].',
            $this->identifier(),
            $attribute,
        ));

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
        $this->attributes->assertEquals($attribute, $value, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] attribute [%s] doesn't equal the given value [%s].",
            $this->identifier(),
            $attribute,
            $value,
        ));

        return $this;
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->attributes->assertDoesntEqual($attribute, $value, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] attribute [%s] equals the given value [%s].',
            $this->identifier(),
            $attribute,
            $value,
        ));

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
        $this->attributes->assertContains($attribute, $value, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] attribute [%s] doesn't contain the given value [%s].",
            $this->identifier(),
            $attribute,
            $value,
        ));

        return $this;
    }

    /** Assert the given element's class doesn't contain the given value. */
    public function assertAttributeDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->attributes->assertDoesntContain($attribute, $value, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] attribute [%s] contains the given value [%s].',
            $this->identifier(),
            $attribute,
            $value,
        ));

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
        $this->attributes->assertStartsWith($attribute, $prefix, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] attribute [%s] doesn't start with the given prefix [%s].",
            $this->identifier(),
            $attribute,
            $prefix,
        ));

        return $this;
    }

    /** Assert the attribute doesn't start with the given prefix. */
    public function assertAttributeDoesntStartWith(string $attribute, string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->attributes->assertDoesntStartWith($attribute, $prefix, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] attribute [%s] starts with the given prefix [%s].',
            $this->identifier(),
            $attribute,
            $prefix,
        ));

        return $this;
    }

    /** Assert the attribute ends with the given prefix. */
    public function assertAttributeEndsWith(string $attribute, string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->attributes->assertEndsWith($attribute, $suffix, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] attribute [%s] doesn't end with the given suffix [%s].",
            $this->identifier(),
            $attribute,
            $suffix,
        ));

        return $this;
    }

    /** Assert the attribute doesn't start with the given prefix. */
    public function assertAttributeDoesntEndWith(string $attribute, string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->attributes->assertDoesntEndWith($attribute, $suffix, $normaliseWhitespace, $message ?? sprintf(
            'The element [%s] attribute [%s] ends with the given suffix [%s].',
            $this->identifier(),
            $attribute,
            $suffix,
        ));

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
     * @param  callable(?string $value): bool  $callback
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
     * @param  callable(?string $value): bool  $callback
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
}
