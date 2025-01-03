<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype;

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

    /** Return whether the assertable element matches the given selectors. */
    public function matches(string $selectors): bool
    {
        return $this->root->matches($selectors);
    }

    /** Return the assertable element matches the given selectors. */
    public function querySelector(string $selectors): ?AssertableHtmlElement
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
