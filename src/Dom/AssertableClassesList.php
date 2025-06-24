<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use ArrayAccess;
use Countable;
use Dom\TokenList;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;
use RuntimeException;
use Stringable;
use Traversable;
use Ziadoz\AssertableHtml\Concerns\Asserts\AssertsClassesList;

final readonly class AssertableClassesList implements ArrayAccess, Countable, IteratorAggregate, Stringable
{
    use AssertsClassesList;

    /** Create a list of assertable classes. */
    public function __construct(private TokenList $classes)
    {
    }

    /** Return the classes (optionally whitespace normalised). */
    public function value(bool $normaliseWhitespace = false): string
    {
        return $normaliseWhitespace
            ? $this->__toString()
            : $this->classes->value;
    }

    /** Return whether the assertable class list is empty. */
    public function empty(): bool
    {
        return count($this) === 0;
    }

    /** Return whether the assertable class list contains the given class. */
    public function contains(string $class): bool
    {
        return $this->classes->contains($class);
    }

    /**
     * Perform a callback on each class in the assertable class list.
     *
     * @param  callable(string $class, int $index): void  $callback
     */
    public function each(callable $callback): self
    {
        array_map($callback, array_values($this->toArray()), array_keys($this->toArray()));

        return $this;
    }

    /**
     * Perform a callback on each class in the assertable class list in sequence.
     *
     * @param  callable(string $class, int $sequence): void  ...$callbacks
     */
    public function sequence(callable ...$callbacks): self
    {
        if (count($callbacks) === 0) {
            throw new InvalidArgumentException('No sequence callbacks given.');
        }

        foreach ($this as $index => $class) {
            $callback = $callbacks[$index] ?? throw new OutOfBoundsException(sprintf(
                'Missing sequence callback for class at position [%d].',
                $index,
            ));

            $callback($class, $index);
        }

        return $this;
    }

    /** Return whether the assertable class list contains any of the given classes. */
    public function any(array $classes): bool
    {
        return array_any(array_values($classes), fn (string $class): bool => $this->classes->contains($class));
    }

    /** Return whether the assertable class list contains all the given classes. */
    public function all(array $classes): bool
    {
        return array_all(array_values($classes), fn (string $class): bool => $this->classes->contains($class));
    }

    /** Dump the assertable class list. */
    public function dump(): void
    {
        dump($this->toArray());
    }

    /** Dump and die the assertable class list. */
    public function dd(): never
    {
        dd($this->toArray());
    }

    /** Return the assertable class list as an array. */
    public function toArray(): array
    {
        return iterator_to_array($this->classes);
    }

    /*
    |--------------------------------------------------------------------------
    | Array Access
    |--------------------------------------------------------------------------
    */

    /** Check a class exists in the assertable class list. */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->classes[(int) $offset]);
    }

    /** Get a class in the assertable class list. */
    public function offsetGet(mixed $offset): ?string
    {
        return $this->classes[(int) $offset];
    }

    /** Unable to add class to the assertable class list. */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unable to add classes to list');
    }

    /** Unable to remove class from the assertable class list. */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unable to remove classes from list');
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /** Return the number of classes in the assertable class list. */
    public function count(): int
    {
        return count($this->classes);
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /** Get an iterator of the assertable class list. */
    public function getIterator(): Traversable
    {
        yield from iterator_to_array($this->classes);
    }

    /*
    |--------------------------------------------------------------------------
    | Stringable
    |--------------------------------------------------------------------------
    */

    /** Return the assertable class list as a string. */
    public function __toString(): string
    {
        return implode(' ', iterator_to_array($this->classes));
    }
}
