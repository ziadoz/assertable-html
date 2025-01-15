<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Element;
use Dom\HTMLElement;
use ReflectionClass;
use Ziadoz\AssertableHtml\Concerns\AssertsHtmlElement;
use Ziadoz\AssertableHtml\Concerns\IdentifiesElement;

readonly class AssertableHtmlElement
{
    use AssertsHtmlElement;
    use IdentifiesElement;

    /** The element's inner HTML. */
    public string $html;

    /** The element's classes. */
    public AssertableClassList $classes;

    /** The element's attributes. */
    public AssertableAttributesList $attributes;

    /** The element's tag (lowercase). */
    public string $tag;

    /** The element's ID. */
    public string $id;

    /** The element's text. */
    public AssertableText $text;

    /** Create an assertable element. */
    public function __construct(private HTMLElement|Element $root)
    {
        $this->html = $this->root->innerHTML;
        $this->classes = new AssertableClassList($this->root->classList);
        $this->attributes = new AssertableAttributesList($this->root->attributes);
        $this->tag = strtolower($this->root->tagName);
        $this->id = $this->root->id;
        $this->text = new AssertableText($this->root->textContent);

        // @todo: Rename $this->root to $this->element.
        // @todo: Document, Parent, Child, Next Sibling, Previous Sibling element proxies only.
        // @todo: Should assertable classes be separate (e.g. $this->classes, $this->>assertableClasses, $this->attributes, $this->assertableAttributes).
    }

    /** Get the underlying HTML element. */
    private function getElement(): HTMLElement|Element
    {
        return $this->root;
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
    public static function proxy(HTMLElement|Element|null $element): ?object
    {
        return $element !== null
            ? new ReflectionClass(static::class)->newLazyProxy(fn () => new static($element))
            : null;
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

    /** Return whether the assertable element contains the given assertable element. */
    public function contains(self $other): bool
    {
        return $this->root->contains($other->getElement());
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
    public function querySelectorAll(string $selectors): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->root->querySelectorAll($selectors));
    }

    /** Return assertable elements matches the given tag. */
    public function getElementsByTagName(string $qualifiedName): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->root->getElementsByTagName($qualifiedName));
    }
}
