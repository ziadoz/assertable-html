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
    public HtmlDocument|Document|HtmlElement|Element $root {
        get {
            return $this->root;
        }
    }

    /** The selector to identify the root element. */
    public string $selector {
        get {
            return $this->selector;
        }
    }

    /** The underlying HTML document instance. */
    public HtmlDocument|Document $document {
        get {
            return $this->root->ownerDocument;
        }
    }

    /** Create an assertable HTML instance. */
    public function __construct(HtmlDocument|Document|HtmlElement|Element $document, string $selector, bool $match = true)
    {
        $this->root = $match ? (new RootElementMatcher)->match($document, $selector) : $document;
        $this->selector = $selector;
    }

    /** Performs assertions on a scoped selection. */
    public function with(string $selector, ?callable $callback = null, bool $append = true): static
    {
        // Determine the custom assertable class for the element (if any).
        $selector = ($append ? $this->selector . ' ' . $selector : $selector);
        $element = (new RootElementMatcher)->match($this->document, $selector);
        $class = (new AssertableElementMatcher)->match($element);

        // Skip matching as we've found the element upfront in order to determine its custom assertable class.
        $instance = new $class($element, $selector, match: false);

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

    /** Return the document HTML. */
    public function getDocumentHtml(): string
    {
        return $this->document->saveHtml();
    }

    /** Return the root element HTML. */
    public function getRootHtml(): string
    {
        return $this->document->saveHtml($this->root);
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
