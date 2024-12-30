<?php

namespace Ziadoz\AssertableHtml\Elements;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use RuntimeException;
use Traversable;

class AssertableElementsCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Create a collection of AssertableElementInterface objects.
     *
     * @param  array<AssertableElementInterface>  $elements
     */
    public function __construct(protected array $elements = [])
    {
        $this->elements = array_values(
            array_filter(
                $this->elements,
                fn (mixed $element): bool => $element instanceof AssertableElementInterface,
            ),
        );
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

    /** Get the element at the nth position in the collection. */
    public function nth(int $position): ?AssertableElementInterface
    {
        return $this->offsetGet($position);
    }

    /*
    |--------------------------------------------------------------------------
    | Array Access
    |--------------------------------------------------------------------------
    */

    /** Check an element exists in the collection. */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[$offset]);
    }

    /** Get an element in the collection. */
    public function offsetGet(mixed $offset): ?AssertableElementInterface
    {
        return $this->elements[$offset];
    }

    /** Add an element to the collection. */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unable to add or replace elements in the collection.');
    }

    /** Remove an element from the collection. */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unable to remove elements from the collection.');
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /** Return the number of elements in the collection. */
    public function count(): int
    {
        return count($this->elements);
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /** Get an iterator of the collection.  */
    public function getIterator(): Traversable
    {
        return yield from $this->elements;
    }
}
