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

    /** Assert the element passes the given callback. */
    public function assertElement(callable $callback): void
    {
        PHPUnit::assertTrue(
            $callback($this->root),
            sprintf(
                'The element [%s] does not pass the given callback.',
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
                'The element [%s] does not match the given selector [%s].',
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
    | Assert Text
    |--------------------------------------------------------------------------
    */

    /** Assert the element's text passes the given callback. */
    public function assertText(callable $callback, bool $stripWhitespace = true): void
    {
        PHPUnit::assertTrue(
            $callback(
                $stripWhitespace
                    ? Utilities::normaliseWhitespace($this->root->textContent)
                    : $this->root->textContent
            ),
            sprintf(
                'The element [%s] text does not pass the given callback.',
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
    public function assertTextEquals(string $text, bool $stripWhitespace = true): void
    {
        PHPUnit::assertSame(
            $text,
            $stripWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            sprintf(
                'The element [%s] text does not equal the given text.',
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    public function assertTextDoesntEqual(string $text, bool $stripWhitespace = true): void
    {
        PHPUnit::assertNotSame(
            $text,
            $stripWhitespace
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
    public function assertTextContains(string $text, bool $stripWhitespace = true): void
    {
        PHPUnit::assertStringContainsString(
            $text,
            $stripWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            sprintf(
                'The element [%s] text does not contain the given text.',
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Alias for assertTextContains() */
    public function assertSeeIn(string $text, bool $stripWhitespace = true): void
    {
        $this->assertTextContains($text, $stripWhitespace);
    }

    /** Assert the element's text doesn't contain the given text. */
    public function assertTextDoesntContain(string $text, bool $stripWhitespace = true): void
    {
        PHPUnit::assertStringNotContainsString(
            $text,
            $stripWhitespace
                ? Utilities::normaliseWhitespace($this->root->textContent)
                : $this->root->textContent,
            sprintf(
                'The element [%s] text contains the given text.',
                Utilities::selectorFromElement($this->root),
            ),
        );
    }

    /** Alias for assertTextDoesntContain() */
    public function assertDontSeeIn(string $text, bool $stripWhitespace = true): void
    {
        $this->assertTextDoesntContain($text, $stripWhitespace);
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the element's classes contains the given class. */
    public function assertClassContains(string $class): void
    {
        PHPUnit::assertTrue(
            $this->root->classList->contains($class),
            sprintf(
                'The element [%s] class does not match the given class [%s].',
                Utilities::selectorFromElement($this->root),
                '.' . $class,
            ),
        );
    }

    /** Assert the element's classes doesn't contain the given class. */
    public function assertClassDoesntContain(string $class): void
    {
        PHPUnit::assertFalse(
            $this->root->classList->contains($class),
            sprintf(
                'The element [%s] class matches the given class [%s].',
                Utilities::selectorFromElement($this->root),
                '.' . $class,
            ),
        );
    }
}
