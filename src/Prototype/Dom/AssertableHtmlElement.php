<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype\Dom;

use Dom\Element;
use Dom\HTMLElement;
use ReflectionClass;
use Ziadoz\AssertableHtml\Prototype\Concerns\AssertsHtml;

readonly class AssertableHtmlElement
{
    use AssertsHtml;

    public string $innerHtml;
    public string $tagName;
    public string $className;
    public string $id;
    public ?string $nodeValue;
    public ?string $textContent;

    public ?self $parentElement;
    public int $childElementCount;
    public ?self $firstElementSibling;
    public ?self $lastElementChild;
    public ?self $previousElementSibling;
    public ?self $nextElementSibling;

    /** Create an assertable element. */
    public function __construct(private HTMLElement|Element $root)
    {
        $this->innerHtml = $this->root->innerHTML;
        $this->tagName = $this->root->tagName;
        $this->className = $this->root->className;
        $this->id = $this->root->id;
        $this->nodeValue = $this->root->nodeValue;
        $this->textContent = $this->root->textContent;

        $this->parentElement = $this->root->parentElement ? static::proxy($this->root->parentElement) : null;
        $this->childElementCount = $this->root->childElementCount;
        $this->firstElementSibling = $this->root->firstElementChild ? static::proxy($this->root->firstElementChild) : null;
        $this->lastElementChild = $this->root->lastElementChild ? static::proxy($this->root->lastElementChild) : null;
        $this->previousElementSibling = $this->root->previousElementSibling ? static::proxy($this->root->previousElementSibling) : null;
        $this->nextElementSibling = $this->root->nextElementSibling ? static::proxy($this->root->nextElementSibling) : null;
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
    | Proxy
    |--------------------------------------------------------------------------
    */

    /** Create a lazy proxy assertable element for the given element. */
    public static function proxy(HTMLElement|Element $element): object
    {
        return new ReflectionClass(static::class)->newLazyProxy(fn () => new static($element));
    }

    /*
    |--------------------------------------------------------------------------
    | Native
    |--------------------------------------------------------------------------
    */

    /** Return the assertable element's attribute names. */
    public function getAttributeNames(): array
    {
        return $this->root->getAttributeNames();
    }

    /** Return the assertable element's given attribute. */
    public function getAttribute(string $qualifiedName): ?string
    {
        return $this->root->getAttribute($qualifiedName);
    }

    /** Return whether the assertable element has attributes. */
    public function hasAttributes(): bool
    {
        return $this->root->hasAttributes();
    }

    /** Return whether the assertable element has the given attribute. */
    public function hasAttribute(string $qualifiedName): bool
    {
        return $this->root->hasAttribute($qualifiedName);
    }

    /** Return the closest matching assertable element. */
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
