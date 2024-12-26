<?php

namespace Ziadoz\AssertableHtml\Elements;

use Dom\Document;
use Dom\HtmlElement;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Utilities;

class AssertableElement implements AssertableElementInterface
{
    /** The root element to perform assertions on.*/
    protected HtmlElement $root;

    /** The selector that was used to select the HTML element. */
    protected string $selector;

    /** Create an assertable HTML element. */
    public function __construct(HtmlElement $element, string $selector)
    {
        $this->root = $this->determineRoot($element, $selector);
        $this->selector = $selector;
    }

    /** Determine the root element to perform assertions on. The root can only ever be a single element. */
    protected function determineRoot(HtmlElement $element, string $selector): HtmlElement
    {
        $nodes = $element->querySelectorAll($selector);

        PHPUnit::assertCount(
            1,
            $nodes,
            trim(sprintf(
                "The element selector [%s] matches %d elements instead of exactly 1 element.\n\n%s",
                $selector,
                count($nodes),
                Utilities::nodesToMatchesHtml($nodes),
            )),
        );

        return $nodes[0];
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
    public function assertElement(callable $callback): void
    {
        PHPUnit::assertTrue(
            $callback($this->root),
            sprintf(
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
    public function assertMatchesSelector(string $selector): void
    {
        PHPUnit::assertTrue(
            $this->root->matches($selector),
            sprintf(
                "The element [%s] doesn't match the given selector [%s].",
                Utilities::selectorFromElement($this->root),
                $selector,
            ),
        );
    }

    /** Assert the element doesn't match the given selector. */
    public function assertDoesntMatchSelector(string $selector): void
    {
        PHPUnit::assertFalse(
            $this->root->matches($selector),
            sprintf(
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

    /** Assert the element contains the exact number of elements matching the given selector. */
    public function assertCountElements(int $expected, string $selector): void
    {
        PHPUnit::assertCount(
            $expected,
            $this->root->querySelectorAll($selector),
            sprintf(
                "The element [%s] doesn't have exactly [%d] elements matching the selector [%s].",
                Utilities::selectorFromElement($this->root),
                $expected,
                $selector,
            ),
        );
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
    public function assertText(callable $callback, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertTrue(
            $callback(
                $normaliseWhitespace
                    ? Utilities::normaliseWhitespace($this->root->textContent)
                    : $this->root->textContent
            ),
            sprintf(
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
    public function assertTextEquals(string $text, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertSame(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            sprintf(
                "The element [%s] text doesn't equal the given text.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Assert the element's text doesn't equal the given text. */
    public function assertTextDoesntEqual(string $text, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertNotSame(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            sprintf(
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
    public function assertTextContains(string $text, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertStringContainsString(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            sprintf(
                "The element [%s] text doesn't contain the given text.",
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Alias for assertTextContains() */
    public function assertSeeIn(string $text, bool $normaliseWhitespace = true): void
    {
        $this->assertTextContains($text, $normaliseWhitespace);
    }

    /** Assert the element's text doesn't contain the given text. */
    public function assertTextDoesntContain(string $text, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertStringNotContainsString(
            $text,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            sprintf(
                'The element [%s] text contains the given text.',
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Alias for assertTextDoesntContain() */
    public function assertDontSeeIn(string $text, bool $normaliseWhitespace = true): void
    {
        $this->assertTextDoesntContain($text, $normaliseWhitespace);
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
    public function assertClass(callable $callback): void
    {
        PHPUnit::assertTrue(
            $callback(iterator_to_array($this->root->classList)),
            sprintf(
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
    public function assertClassEquals(string $class, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertSame(
            $class,
            $normaliseWhitespace
                ? implode(' ', iterator_to_array($this->root->classList))
                : $this->root->classList->value,
            sprintf(
                "The element [%s] class doesn't equal the given class [%s].",
                Utilities::selectorFromElement($this->root),
                $class,
            ),
        );
    }

    /** Assert the element's class doesn't equal the given class. */
    public function assertClassDoesntEqual(string $class, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertNotSame(
            $class,
            $normaliseWhitespace
                ? implode(' ', iterator_to_array($this->root->classList))
                : $this->root->classList->value,
            sprintf(
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
    public function assertClassContains(string $class): void
    {
        PHPUnit::assertTrue(
            $this->root->classList->contains($class),
            sprintf(
                "The element [%s] class doesn't contain the given class [%s].",
                Utilities::selectorFromElement($this->root),
                $class,
            ),
        );
    }

    /** Assert the element's class doesn't contain the given class. */
    public function assertClassDoesntContain(string $class): void
    {
        PHPUnit::assertFalse(
            $this->root->classList->contains($class),
            sprintf(
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
    public function assertClassContainsAll(array $classes): void
    {
        $classes = array_values($classes);

        PHPUnit::assertTrue(
            array_intersect(iterator_to_array($this->root->classList), $classes) === $classes,
            sprintf(
                "The element [%s] class doesn't contain all the given classes [%s].",
                Utilities::selectorFromElement($this->root),
                implode(' ', $classes),
            ),
        );
    }

    /** Assert the element's class doesn't contain all the given classes. */
    public function assertClassDoesntContainAll(array $classes): void
    {
        $classes = array_values($classes);

        PHPUnit::assertFalse(
            array_intersect(iterator_to_array($this->root->classList), $classes) === $classes,
            sprintf(
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
     * @param  callable(string|null $value): bool  $callback
     */
    public function assertAttribute(string $attribute, callable $callback): void
    {
        PHPUnit::assertTrue(
            $callback($this->root->getAttribute($attribute)),
            sprintf(
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
    public function assertAttributeEquals(string $attribute, string $value, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertSame(
            $value,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace((string) $this->root->getAttribute($attribute))
                : (string) $this->root->getAttribute($attribute),
            sprintf(
                "The element [%s] attribute [%s] doesn't equal the given value [%s].",
                Utilities::selectorFromElement($this->root),
                $attribute,
                $value,
            ),
        );
    }

    /** Assert the given element's attribute doesn't equal the given value. */
    public function assertAttributeDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = true): void
    {
        PHPUnit::assertNotSame(
            $value,
            $normaliseWhitespace
                ? Utilities::normaliseWhitespace((string) $this->root->getAttribute($attribute))
                : (string) $this->root->getAttribute($attribute),
            sprintf(
                'The element [%s] attribute [%s] equals the given value [%s].',
                Utilities::selectorFromElement($this->root),
                $attribute,
                $value,
            ),
        );
    }
}
