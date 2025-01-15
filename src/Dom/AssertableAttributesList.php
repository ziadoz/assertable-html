<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use ArrayAccess;
use Countable;
use Dom\NamedNodeMap;
use IteratorAggregate;
use RuntimeException;
use Traversable;
use Ziadoz\AssertableHtml\Concerns\AssertsAttributes;
use Ziadoz\AssertableHtml\Support\Whitespace;

readonly class AssertableAttributesList implements ArrayAccess, Countable, IteratorAggregate
{
    use AssertsAttributes;

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

    /** Return whether the attribute is present in the assertable attribute list. */
    public function present(string $attribute): bool
    {
        return $this->offsetExists($attribute);
    }

    /** Return whether the attribute is missing in the assertable attribute list. */
    public function missing(string $attribute): bool
    {
        return ! $this->present($attribute);
    }

    /** Return whether the given attribute starts with the given value in the assertable attribute list. */
    public function startsWith(string $attribute, string $value): bool
    {
        return str_starts_with($this->attributes[$attribute] ?? '', $value);
    }

    /** Return whether the given attribute ends with the given value in the assertable attribute list. */
    public function endsWith(string $attribute, string $value): bool
    {
        return str_ends_with($this->attributes[$attribute] ?? '', $value);
    }

    /** Return whether the given attribute contains the given value in the assertable attribute list. */
    public function contains(string $attribute, string $value): bool
    {
        return str_contains($this->attributes[$attribute] ?? '', $value);
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
