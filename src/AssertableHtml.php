<?php

namespace Ziadoz\AssertableHtml;

use Dom\Document;
use Dom\HtmlDocument;
use Dom\HtmlElement;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Matchers\RootElementMatcher;

class AssertableHtml
{
    /** The root HTML document or element to perform assertions on. */
    protected HtmlDocument|HtmlElement $root;

    /** The selector to identify the root element. */
    protected string $selector;

    /** Create an assertable HTML instance. */
    public function __construct(HtmlDocument|Document|HtmlElement $document, string $selector)
    {
        $this->root = (new RootElementMatcher)->match($document, $selector);
        $this->selector = $selector;
    }

    /** Performs assertions on a scoped selection. */
    public function with(string $selector, ?callable $callback = null, bool $prepend = true): static
    {
        $instance = new static($this->getDocument(), ($prepend ? $this->selector . ' ' . $selector : $selector));

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

    /** Perform assertions on an exact HTML element. */
    public function element(string $selector, ?callable $callback = null): AssertableElement
    {
        $root = (new RootElementMatcher)->match($this->root, $this->selector . ' ' . $selector);

        $element = new AssertableElement($root);

        if ($callback) {
            $callback($element);
        }

        return $element;
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
