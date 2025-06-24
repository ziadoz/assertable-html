<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Element;
use Dom\HTMLElement;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Concerns\AssertsElement;
use Ziadoz\AssertableHtml\Concerns\IdentifiesElement;
use Ziadoz\AssertableHtml\Concerns\Scopeable;
use Ziadoz\AssertableHtml\Concerns\Targetable;
use Ziadoz\AssertableHtml\Concerns\Whenable;

readonly class AssertableElement
{
    use AssertsElement;
    use IdentifiesElement;
    use Scopeable;
    use Targetable;
    use Whenable;

    /** The element's inner HTML. */
    public string $html;

    /** The element's classes. */
    public AssertableClassesList $classes;

    /** The element's attributes. */
    public AssertableAttributesList $attributes;

    /** The element's tag (lowercase). */
    public string $tag;

    /** The element's ID. */
    public string $id;

    /** The element's text. */
    public AssertableText $text;

    /** Create an assertable element. */
    public function __construct(private HTMLElement|Element $element)
    {
        // Properties
        $this->html = $this->element->innerHTML;
        $this->classes = new AssertableClassesList($this->element->classList);
        $this->attributes = new AssertableAttributesList($this->element->attributes);
        $this->tag = strtolower($this->element->tagName);
        $this->id = $this->element->id;
        $this->text = new AssertableText($this->element->textContent);
    }

    /** Get the underlying HTML element. */
    private function getElement(): HTMLElement|Element
    {
        return $this->element;
    }

    /** Get the assertable element HTML. */
    public function getHtml(): string
    {
        return $this->element->ownerDocument->saveHtml($this->element);
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

    /*
    |--------------------------------------------------------------------------
    | Native
    |--------------------------------------------------------------------------
    */

    /** Return whether the assertable element contains the given assertable element. */
    public function contains(self $other): bool
    {
        return $this->element->contains($other->getElement());
    }

    /** Return the closest matching assertable element. */
    public function closest(string $selector): static
    {
        if (($element = $this->element->closest($selector)) === null) {
            PHPUnit::fail(sprintf(
                "The element [%s] doesn't have a closest element matching the given selector [%s].",
                $this->identifier(),
                $selector,
            ));
        }

        return new static($element);
    }

    /** Return whether the assertable element matches the given selector. */
    public function matches(string $selector): bool
    {
        return $this->element->matches($selector);
    }

    /** Return the assertable element matches the given selector. */
    public function querySelector(string $selector): static
    {
        if (($element = $this->element->querySelector($selector)) === null) {
            PHPUnit::fail(sprintf(
                "The element [%s] doesn't contain an element matching the given selector [%s].",
                $this->identifier(),
                $selector,
            ));
        }

        return new static($element);
    }

    /** Return assertable elements matches the given selector. */
    public function querySelectorAll(string $selector): AssertableElementsList
    {
        return new AssertableElementsList($this->element->querySelectorAll($selector));
    }

    /** Return assertable elements matches the given tag. */
    public function getElementsByTagName(string $tag): AssertableElementsList
    {
        return new AssertableElementsList($this->element->getElementsByTagName($tag));
    }
}
