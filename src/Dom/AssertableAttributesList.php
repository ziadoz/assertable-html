<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use ArrayAccess;
use Countable;
use Dom\NamedNodeMap;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;
use RuntimeException;
use Traversable;
use Ziadoz\AssertableHtml\Concerns\Asserts\AssertsAttributesList;
use Ziadoz\AssertableHtml\Support\Whitespace;

final readonly class AssertableAttributesList implements ArrayAccess, Countable, IteratorAggregate
{
    use AssertsAttributesList;

    /** The element attributes */
    private array $attributes;

    /** Create a list of assertable attributes. */
    public function __construct(NamedNodeMap $map)
    {
        $this->attributes = $this->mapToAssocArray($map);
    }

    /** Convert the \Dom\NamedNodeMap to an associative array. */
    private function mapToAssocArray(NamedNodeMap $map): array
    {
        $attributes = [];

        foreach ($map as $key => $attr) {
            $attributes[$key] = $attr->value;
        }

        return $attributes;
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

    /** Return the attribute (optionally whitespace normalised). */
    public function value(string $attribute, bool $normaliseWhitespace = false): string
    {
        if (! $this->offsetExists($attribute)) {
            return '';
        }

        return $normaliseWhitespace
            ? Whitespace::normalise($this->attributes[$attribute])
            : $this->attributes[$attribute];
    }

    /** Return whether the assertable attribute list is empty. */
    public function empty(): bool
    {
        return count($this->attributes) === 0;
    }

    /** Return the names of the attributes of the attributes in the assertable attribute list. */
    public function names(): array
    {
        return array_keys($this->attributes);
    }

    /** Return whether assertable attribute list has the given attribute in it. */
    public function has(string $attribute): bool
    {
        return $this->offsetExists($attribute);
    }

    /**
     * Perform a callback on each attribute in the assertable attribute list.
     *
     * @param  callable(string $attribute, ?string $value, int $index): void  $callback
     */
    public function each(callable $callback): self
    {
        array_map($callback, array_keys($this->attributes), array_values($this->attributes), range(0, count($this->attributes) - 1));

        return $this;
    }

    /**
     * Perform a callback on each attribute in the assertable attribute list in sequence.
     *
     * @param  callable(string $attribute, ?string $value, int $sequence): void  ...$callbacks
     */
    public function sequence(callable ...$callbacks): self
    {
        if (count($callbacks) === 0) {
            throw new InvalidArgumentException('No sequence callbacks given.');
        }

        $index = 0;

        foreach ($this as $attribute => $value) {
            $callback = $callbacks[$index] ?? throw new OutOfBoundsException(sprintf(
                'Missing sequence callback for attribute [%s] at position [%d].',
                $attribute,
                $index,
            ));

            $callback($attribute, $value, $index);
            $index++;
        }

        return $this;
    }

    /** Return the assertable class list as an array. */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /*
    |--------------------------------------------------------------------------
    | Array Access
    |--------------------------------------------------------------------------
    */

    /** Check an attribute exists in the assertable attribute list. */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[(string) $offset]);
    }

    /** Get an attribute in the assertable attribute list. */
    public function offsetGet(mixed $offset): ?string
    {
        return $this->attributes[(string) $offset];
    }

    /** Unable to add attribute to the assertable attribute list. */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unable to add attributes to list');
    }

    /** Unable to remove attribute from the assertable attribute list. */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unable to remove attributes from list');
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /** Return the number of attributes in the assertable attribute list. */
    public function count(): int
    {
        return count($this->attributes);
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /** Get an iterator of the assertable attribute list. */
    public function getIterator(): Traversable
    {
        yield from $this->attributes;
    }

    /*
    |--------------------------------------------------------------------------
    | Stringable
    |--------------------------------------------------------------------------
    */

    /** Return the assertable attribute list as a string. */
    public function __toString(): string
    {
        $strings = [];

        foreach ($this->attributes as $key => $value) {
            $strings[] = sprintf('%s="%s"', $key, $value);
        }

        return implode(' ', $strings);
    }
}
