<?php

namespace Ziadoz\AssertableHtml\Elements;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class AssertableElementsCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /** Create a collection of AssertableElementInterface objects.  */
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

    /** Filter the elements in the collection. */
    public function filter(callable $callback): static
    {
        return new static(array_filter($this->elements, $callback));
    }

    /** Perform a callback on each element in the collection. */
    public function each(callable $callback): void
    {
        array_map($callback, $this->elements);
    }

    /** Slice elements from the collection. */
    public function slice(int $offset = 0, ?int $length = null): static
    {
        return new static(array_slice($this->elements, $offset, $length));
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
        if (! $value instanceof AssertableElementInterface) {
            throw new InvalidArgumentException(sprintf(
                'Only instances of [%s] can be added to [%s]',
                AssertableElementInterface::class,
                static::class,
            ));
        }

        if (is_null($offset)) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }

    /** Remove an element from the collection. */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->elements[$offset]);
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
