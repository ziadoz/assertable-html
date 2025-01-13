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
use Ziadoz\AssertableHtml\Prototype\Concerns\AssertsHtmlElementList;

readonly class AssertableHtmlElementsList implements ArrayAccess, Countable, IteratorAggregate
{
    use AssertsHtmlElementList;

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
    }

    /** Get the assertable element list HTML. */
    public function getHtml(): string
    {
        return implode("\n", array_map(
            fn (AssertableHtmlElement $element): string => $element->getHtml(),
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

    /** Get the assertable element at the nth position in the assertable element list. */
    public function nth(int $index): ?AssertableHtmlElement
    {
        return $this->offsetGet($index);
    }

    /** Return the first assertable element in the assertable element list.  */
    public function first(): ?AssertableHtmlElement
    {
        return $this->offsetGet(0);
    }

    /** Return the last assertable element in the assertable element list.  */
    public function last(): ?AssertableHtmlElement
    {
        return $this->offsetGet(count($this) - 1);
    }

    /** Perform a callback on each assert element in the list. */
    public function each(callable $callback): static
    {
        array_map($callback, $this->elements);

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
    public function offsetGet(mixed $offset): ?AssertableHtmlElement
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
