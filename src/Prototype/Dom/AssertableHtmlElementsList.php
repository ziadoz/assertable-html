<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype\Dom;

use ArrayAccess;
use Countable;
use Dom\Element;
use Dom\HTMLCollection;
use Dom\HTMLElement;
use Dom\NodeList;
use IteratorAggregate;
use RuntimeException;
use Traversable;

readonly class AssertableHtmlElementsList implements ArrayAccess, Countable, IteratorAggregate
{
    /** The length of the collection of elements. */
    public int $length;

    /** The assertable elements. */
    private array $elements;

    /** Create a list of assertable elements. */
    public function __construct(NodeList|HTMLCollection $nodes)
    {
        $this->elements = array_values(
            array_map(
                fn (HTMLElement|Element $element): AssertableHtmlElement => new AssertableHtmlElement($element),
                iterator_to_array($nodes),
            ),
        );

        $this->length = count($this->elements);
    }

    /** Get the assertable list HTML. */
    public function getHtml(): string
    {
        return implode("\n", array_map(
            fn (AssertableHtmlElement $element): string => $element->getHtml(),
            $this->elements,
        ));
    }

    /** Dump the assertable list. */
    public function dump(): void
    {
        dump($this->getHtml());
    }

    /** Dump and die the assertable list. */
    public function dd(): never
    {
        dd($this->getHtml());
    }

    /** Get the assertable element at the nth position in the assertable list. */
    public function nth(int $index): ?AssertableHtmlElement
    {
        return $this->offsetGet($index);
    }

    /*
    |--------------------------------------------------------------------------
    | Array Access
    |--------------------------------------------------------------------------
    */

    /** Check an assertable element exists in the assertable list. */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[(int) $offset]);
    }

    /** Get an assertable element in the assertable list. */
    public function offsetGet(mixed $offset): ?AssertableHtmlElement
    {
        return $this->elements[(int) $offset];
    }

    /** Unable to add an assertable element to the assertable list. */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unable to add elements to list');
    }

    /** Unable to remove an assertable element from the assertable list. */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unable to remove elements from list');
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /** Return the number of assertable elements in the assertable list. */
    public function count(): int
    {
        return $this->length;
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /** Get an iterator of the assertable list. */
    public function getIterator(): Traversable
    {
        yield from $this->elements;
    }
}
