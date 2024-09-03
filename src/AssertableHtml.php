<?php
namespace Ziadoz\AssertHtml;

use Dom\HtmlDocument;
use Dom\HtmlElement;
use PHPUnit\Framework\Assert as PHPUnit;

class AssertableHtml
{
    /**
     * The document to perform assertions on.
     */
    protected HtmlDocument|HtmlElement $document;

    /**
     * The selector to identify the root element.
     */
    protected string $selector;

    /**
     * The root element to perform assertions on.
     */
    protected HtmlElement $root;

    /**
     * Create an assertable HTML instance.
     */
    public function __construct(HtmlDocument|HtmlElement $document, $selector = 'body')
    {
        $this->document = $document;
        $this->selector = $selector;
        $this->root = $this->determineRoot();
    }

    /**
     * Determine the root element to perform assertions on. The root can only ever be a single element.
     */
    protected function determineRoot(): HtmlElement
    {
        $nodes = $this->document->querySelectorAll($this->selector);

        PHPUnit::assertCount(
            1,
            $nodes,
            sprintf('The selector [%s] matches %d elements instead of exactly one element.', $this->selector, count($nodes)),
        );

        return $nodes[0];
    }

    /**
     * Performs assertions on a scoped selection.
     */
    public function with(string $selector, ?callable $callback = null, bool $prepend = true): static
    {
        $instance = new static($this->document, $prepend ? $this->selector . ' ' . $selector : $selector);

        if ($callback) {
            $callback($instance);
        }

        return $instance;
    }

    /**
     * Perform assertions outside the scoped selection.
     */
    public function elsewhere(string $selector, ?callable $callback = null): static
    {
        return $this->with($selector, $callback, false);
    }

    /**
     * Return the underlying HTML document instance.
     */
    public function getDocument(): HtmlDocument
    {
        return $this->document;
    }

    /**
     * Return the underlying root HTML element instance.
     */
    public function getRoot(): HtmlElement
    {
        return $this->root;
    }

    /**
     * Return the document HTML.
     */
    public function getDocumentHtml(): string
    {
        return $this->document->saveHtml();
    }

    /**
     * Return the root element HTML.
     */
    public function getRootHtml(): string
    {
        return $this->document->saveHtml($this->root);
    }

    /**
     * Dump the root element HTML.
     */
    public function dump(): void
    {
        dump($this->getRootHtml());
    }

    /**
     * Dump and die the root element HTML.
     */
    public function dd(): never
    {
        dd($this->getRootHtml());
    }

    /**
     * Create an assertable HTML instance from an HTML string.
     */
    public static function createFromString(string $content, string $selector = 'body'): static
    {
        return new static(HtmlDocument::createFromString($content), $selector);
    }
}
