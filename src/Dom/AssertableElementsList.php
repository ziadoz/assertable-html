<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use ArrayAccess;
use Countable;
use Dom\Element;
use Dom\HTMLCollection;
use Dom\HTMLElement;
use Dom\NodeList;
use IteratorAggregate;
use RuntimeException;
use Traversable;
use Ziadoz\AssertableHtml\Concerns\AssertsElementList;
use Ziadoz\AssertableHtml\Concerns\Scopeable;

final readonly class AssertableElementsList implements ArrayAccess, Countable, IteratorAggregate
{
    use AssertsElementList;
    use Scopeable;

    /** The assertable elements. */
    private array $elements;

    /** Create a list of assertable elements. */
    public function __construct(NodeList|HTMLCollection $nodes)
    {
        $this->elements = array_values(
            array_map(
                fn (HTMLElement|Element $element): AssertableElement => new AssertableElement($element),
                $nodes instanceof NodeList
                    ? iterator_to_array($nodes)
                    : $this->htmlCollectionToArray($nodes),
            ),
        );
    }

    /** Convert a \Dom\HTMLCollection instance to an array. */
    private function htmlCollectionToArray(HTMLCollection $nodes): array
    {
        $array = [];

        foreach ($nodes as $node) {
            $array[] = $node;
        }

        return $array;
    }

    /** Get the assertable element list HTML. */
    public function getHtml(): string
    {
        return implode("\n", array_map(
            fn (AssertableElement $element): string => $element->getHtml(),
            $this->elements,
        ));
    }

    /** Dump the assertable element list. */
    public function dump(): void
    {
        dump($this->getHtml());
    }

    /** Dump and die the assertable element list. */
    public function dd(): never
    {
        dd($this->getHtml());
    }

    /** Return whether the assertable element list is empty. */
    public function empty(): bool
    {
        return count($this) === 0;
    }

    /** Get the assertable element at the nth position in the assertable element list. */
    public function nth(int $index): ?AssertableElement
    {
        return $this->offsetGet($index);
    }

    /** Return the first assertable element in the assertable element list.  */
    public function first(): ?AssertableElement
    {
        return $this->offsetGet(0);
    }

    /** Return the last assertable element in the assertable element list.  */
    public function last(): ?AssertableElement
    {
        return $this->offsetGet(count($this) - 1);
    }

    /** Perform a callback on each assert element in the list. */
    public function each(callable $callback): self
    {
        array_map($callback, array_values($this->elements), array_keys($this->elements));

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Array Access
    |--------------------------------------------------------------------------
    */

    /** Check an assertable element exists in the assertable element list. */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->elements[(int) $offset]);
    }

    /** Get an assertable element in the assertable element list. */
    public function offsetGet(mixed $offset): ?AssertableElement
    {
        return $this->elements[(int) $offset];
    }

    /** Unable to add an assertable element to the assertable element list. */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Unable to add elements to list');
    }

    /** Unable to remove an assertable element from the assertable element list. */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Unable to remove elements from list');
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /** Return the number of assertable elements in the assertable element list. */
    public function count(): int
    {
        return count($this->elements);
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /** Get an iterator of the assertable element list. */
    public function getIterator(): Traversable
    {
        yield from $this->elements;
    }
}
