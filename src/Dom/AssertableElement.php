<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Element;
use Dom\HTMLElement;
use ReflectionClass;
use Ziadoz\AssertableHtml\Concerns\AssertsElement;
use Ziadoz\AssertableHtml\Concerns\IdentifiesElement;
use Ziadoz\AssertableHtml\Concerns\Scopeable;
use Ziadoz\AssertableHtml\Concerns\Whenable;
use Ziadoz\AssertableHtml\Concerns\Withable;

readonly class AssertableElement
{
    use AssertsElement;
    use IdentifiesElement;
    use Scopeable;
    use Whenable;
    use Withable;

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
    public function __construct(private HTMLElement|Element $element)
    {
        // Properties
        $this->html = $this->element->innerHTML;
        $this->classes = new AssertableClassList($this->element->classList);
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
    public function closest(string $selectors): ?static
    {
        return ($element = $this->element->closest($selectors)) !== null
            ? new static($element)
            : null;
    }

    /** Return whether the assertable element matches the given selectors. */
    public function matches(string $selectors): bool
    {
        return $this->element->matches($selectors);
    }

    /** Return the assertable element matches the given selectors. */
    public function querySelector(string $selectors): ?static
    {
        return ($element = $this->element->querySelector($selectors)) !== null
            ? new static($element)
            : null;
    }

    /** Return assertable elements matches the given selectors. */
    public function querySelectorAll(string $selectors): AssertableElementsList
    {
        return new AssertableElementsList($this->element->querySelectorAll($selectors));
    }

    /** Return assertable elements matches the given tag. */
    public function getElementsByTagName(string $tag): AssertableElementsList
    {
        return new AssertableElementsList($this->element->getElementsByTagName($tag));
    }
}
