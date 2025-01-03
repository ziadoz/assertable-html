<?php

namespace Ziadoz\AssertableHtml\Prototype;

use ArrayAccess;
use Countable;
use Dom\Element;
use Dom\HTMLCollection;
use Dom\HTMLElement;
use Dom\NodeList;
use IteratorAggregate;
use RuntimeException;
use Traversable;

readonly class AssertableHtmlElementsCollection implements ArrayAccess, Countable, IteratorAggregate
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

    /** Dump the collection. */
    public function dump(): void
    {
        dump($this->elements);
    }

    /** Dump and die the collection. */
    public function dd(): never
    {
        dd($this->elements);
    }

    /** Get the assertable element at the nth position in the collection. */
    public function nth(int $index): ?AssertableHtmlElement
    {
        return $this->offsetGet($index);
    }

    /*
    |--------------------------------------------------------------------------
    | Array Access
    |--------------------------------------------------------------------------
    */

    /** Check an assertable element exists in the collection. */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[(int) $offset]);
    }

    /** Get an assertable element in the collection. */
    public function offsetGet(mixed $offset): ?AssertableHtmlElement
    {
        return $this->elements[(int) $offset];
    }

    /** Unable to add an assertable element to the collection. */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unable to add elements to collection');
    }

    /** Unable to remove an assertable element from the collection. */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unable to remove elements from collection');
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /** Return the number of assertable elements in the collection. */
    public function count(): int
    {
        return $this->length;
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /** Get an iterator of the collection. */
    public function getIterator(): Traversable
    {
        yield from $this->elements;
    }
}
