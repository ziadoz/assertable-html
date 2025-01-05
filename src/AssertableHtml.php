<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml;

use Dom\Document;
use Dom\Element;
use Dom\HtmlDocument;
use Dom\HtmlElement;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Matchers\AssertableElementMatcher;
use Ziadoz\AssertableHtml\Matchers\RootElementMatcher;
use Ziadoz\AssertableHtml\Prototype\Concerns\AssertsHtmlElement;

class AssertableHtml
{
    use AssertsHtmlElement;

    /** The root HTML document or HTML element to perform assertions on. */
    protected HtmlDocument|HtmlElement $root;

    /** The selector to identify the root element. */
    protected string $selector;

    /** Create an assertable HTML instance. */
    public function __construct(HtmlDocument|Document|HtmlElement|Element $document, string $selector)
    {
        $this->root = (new RootElementMatcher)->match($document, $selector);
        $this->selector = $selector;
    }

    /** Performs assertions on a scoped selection. */
    public function with(string $selector, ?callable $callback = null, bool $append = true): static
    {
        $instance = new static($this->getDocument(), ($append ? $this->selector . ' ' . $selector : $selector))->toElement();

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

    /** Return the underlying HTML document instance. */
    public function getDocument(): HtmlDocument|Document
    {
        return $this->root->ownerDocument;
    }

    /** Return the root HTML document or element assertions are being performed on. */
    public function getRoot(): HtmlDocument|Document|HtmlElement|Element
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

    /** Promote the root element to the matching assertable class. */
    public function toElement(): AssertableElement
    {
        $class = (new AssertableElementMatcher)->match($this->root);

        return new $class($this->getDocument(), $this->getSelector());
    }
}
