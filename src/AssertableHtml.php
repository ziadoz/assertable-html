<?php

namespace Ziadoz\AssertableHtml;

use Dom\Document;
use Dom\Element;
use Dom\HtmlDocument;
use Dom\HtmlElement;
use Ziadoz\AssertableHtml\Concerns\AssertsHtml;
use Ziadoz\AssertableHtml\Matchers\AssertableElementMatcher;
use Ziadoz\AssertableHtml\Matchers\RootElementMatcher;

class AssertableHtml
{
    use AssertsHtml;

    /** The root HTML document or HTML element to perform assertions on. */
    protected HtmlDocument|HtmlElement $root;

    /** The selector to identify the root element. */
    protected string $selector;

    /** Create an assertable HTML instance. */
    public function __construct(HtmlDocument|Document|HtmlElement|Element $document, string $selector, bool $match = true)
    {
        $this->root = $match ? (new RootElementMatcher)->match($document, $selector) : $document;
        $this->selector = $selector;
    }

    /** Performs assertions on a scoped selection. */
    public function with(string $selector, ?callable $callback = null, bool $append = true): static
    {
        $selector = ($append ? $this->selector . ' ' . $selector : $selector);
        $element = (new RootElementMatcher)->match($this->getDocument(), $selector);
        $class = (new AssertableElementMatcher)->match($element);
        $instance = new $class($element, $selector, false);

        if ($callback) {
            $callback($instance);
        }

        return $instance;
    }

    /** Perform assertions outside the scoped selection. */
    public function elsewhere(string $selector, ?callable $callback = null): static
    {
        return $this->with($selector, $callback, false);
    }

    /** Return the underlying HTML document instance. */
    public function getDocument(): Document
    {
        return $this->root->ownerDocument;
    }

    /** Return the root HTML document or element assertions are being performed on. */
    public function getRoot(): HtmlDocument|HtmlElement
    {
        return $this->root;
    }

    /** Return the selector used to find the root element. */
    public function getSelector(): string
    {
        return $this->selector;
    }

    /** Return the document HTML. */
    public function getDocumentHtml(): string
    {
        return $this->getDocument()->saveHtml();
    }

    /** Return the root element HTML. */
    public function getRootHtml(): string
    {
        return $this->getDocument()->saveHtml($this->getRoot());
    }

    /** Dump the root element HTML. */
    public function dump(): void
    {
        dump($this->getRootHtml());
    }

    /** Dump and die the root element HTML. */
    public function dd(): never
    {
        dd($this->getRootHtml());
    }
}
