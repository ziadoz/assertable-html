<?php

namespace Ziadoz\AssertableHtml\Prototype;

use ArrayAccess;
use Countable;
use Dom\HTMLCollection;
use Dom\NodeList;
use IteratorAggregate;
use RuntimeException;
use Traversable;

readonly class AssertableHtmlElementsList implements ArrayAccess, Countable, IteratorAggregate
{
    public int $length;

    private array $elements;

    public function __construct(NodeList|HTMLCollection $elements)
    {
        $assertables = [];

        foreach ($elements as $offset => $element) {
            $assertables[$offset] = new AssertableHtmlElement($element);
        }

        $this->elements = $assertables;
        $this->length = count($assertables);
    }

    public function item(int $index): ?AssertableHtmlElement
    {
        return $this->offsetGet($index);
    }

    // @todo: Dowe need a separate AssertableHtmlElementsCollection class that is this class, but minus item() and plus namedItem(). YES.
    public function namedItem(string $key): ?AssertableHtmlElement
    {
        return $this->offsetGet($key);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->elements[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unable to add elements to ' . static::class);
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unable to remove elements from ' . static::class);
    }

    public function count(): int
    {
        return $this->length;
    }

    public function getIterator(): Traversable
    {
        yield from $this->elements;
    }
}
