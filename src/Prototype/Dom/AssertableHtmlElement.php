<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype\Dom;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Concerns\AssertsHtml;

readonly class AssertableHtmlElement
{
    use AssertsHtml;

    public string $tagName;
    public string $className;
    public string $id;
    public string $textContent;

    /** Create an assertable element. */
    public function __construct(private HTMLElement|Element $root)
    {
        $this->tagName = $this->root->tagName;
        $this->className = $this->root->className;
        $this->id = $this->root->id;
        $this->textContent = $this->root->textContent;

        //  public ?Element $firstElementChild;
        //    public ?Element $lastElementChild;
        //    public int $childElementCount;
        //    public ?Element $previousElementSibling;
        //    public ?Element $nextElementSibling;

        // $classes array
        // $attributes array
    }

    /** Get the assertable element HTML. */
    public function getHtml(): string
    {
        return $this->root->ownerDocument->saveHtml($this->root);
    }

    /** Dump the assertable element. */
    public function dump(): void
    {
        dump($this->getHtml());
    }

    /** Dump and die the assertable element. */
    public function dd(): never
    {
        dd($this->getHtml());
    }

    /**
     * Return a value if the given condition is true, otherwise return a default.
     *
     * @param  callable(static $assertable): bool|bool  $condition
     * @param  callable(static $assertable): bool|mixed  $value
     * @param  callable(static $assertable): bool|mixed  $default
     */
    public function when(callable|bool $condition, mixed $value, mixed $default = null): mixed
    {
        $condition = is_callable($condition) ? $condition($this) : $condition;

        if ($condition) {
            return is_callable($value) ? $value($this) : $value;
        }

        return is_callable($default) ? $default($this) : $default;
    }

    /*
    |--------------------------------------------------------------------------
    | Native
    |--------------------------------------------------------------------------
    */

    public function getAttributeNames(): array
    {
        return $this->root->getAttributeNames();
    }

    public function getAttribute(string $qualifiedName): ?string
    {
        return $this->root->getAttribute($qualifiedName);
    }

    public function hasAttributes(): bool
    {
        return $this->root->hasAttributes();
    }

    public function hasAttribute(string $qualifiedName): bool
    {
        return $this->root->hasAttribute($qualifiedName);
    }

    public function closest(string $selectors): ?static
    {
        return ($element = $this->root->closest($selectors)) !== null
            ? new static($element)
            : null;
    }

    /** Return whether the assertable element matches the given selectors. */
    public function matches(string $selectors): bool
    {
        return $this->root->matches($selectors);
    }

    /** Return the assertable element matches the given selectors. */
    public function querySelector(string $selectors): ?static
    {
        return new AssertableHtmlElement($this->root->querySelector($selectors));
    }

    /** Return assertable elements matches the given selectors. */
    public function querySelectorAll(string $selectors): AssertableHtmlElementsCollection
    {
        return new AssertableHtmlElementsCollection($this->root->querySelectorAll($selectors));
    }

    /** Return assertable elements matches the given tag. */
    public function getElementsByTagName(string $qualifiedName): AssertableHtmlElementsCollection
    {
        return new AssertableHtmlElementsCollection($this->root->getElementsByTagName($qualifiedName));
    }
}
