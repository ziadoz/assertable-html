<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Document;
use Dom\HTMLDocument;

readonly class AssertableHtmlDocument
{
    /** The document's head. */
    public ?AssertableHtmlElement $head;

    /** The document's body. */
    public ?AssertableHtmlElement $body;

    /** The document's page title. */
    public string $title;

    /** Create a new assertable document. */
    public function __construct(private HTMLDocument|Document $document)
    {
        $this->head = AssertableHtmlElement::proxy($this->document->head);
        $this->body = AssertableHtmlElement::proxy($this->document->body);
        $this->title = $this->document->title;
    }

    /** Get the assertable document HTML. */
    public function getHtml(): string
    {
        return $this->document->saveHtml();
    }

    /** Dump the document HTML. */
    public function dump(): void
    {
        dump($this->getHtml());
    }

    /** Dump and die the document HTML. */
    public function dd(): never
    {
        dd($this->getHtml());
    }

    /** Scope assertable elements matching the given selector. */
    public function with(string $selectors, ?callable $callback): AssertableHtmlElement|AssertableHtmlElementsList
    {
        $elements = count($results = $this->querySelectorAll($selectors)) === 1
            ? $results[0]
            : $results;

        if ($callback) {
            $callback($elements);
        }

        return $elements;
    }

    /*
    |--------------------------------------------------------------------------
    | Native
    |--------------------------------------------------------------------------
    */

    /** Create an empty assertable document. */
    public static function createEmpty(string $encoding = 'UTF-8'): static
    {
        return new static(HTMLDocument::createEmpty());
    }

    /** Create an assertable document from a file. */
    public static function createFromFile(string $path, int $options = 0, ?string $overrideEncoding = null): static
    {
        return new static(HTMLDocument::createFromFile($path, $options, $overrideEncoding));
    }

    /** Create an assertable document from a string. */
    public static function createFromString(string $source, int $options = 0, ?string $overrideEncoding = null): static
    {
        return new static(HTMLDocument::createFromString($source, $options, $overrideEncoding));
    }

    /** Return the assertable element matching the given selectors. */
    public function querySelector(string $selectors): ?AssertableHtmlElement
    {
        return new AssertableHtmlElement($this->document->querySelector($selectors));
    }

    /** Return assertable elements matching the given selectors. */
    public function querySelectorAll(string $selectors): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->document->querySelectorAll($selectors));
    }

    /** Return an assertable element matching the given ID. */
    public function getElementById(string $elementId): ?AssertableHtmlElement
    {
        return ($element = $this->document->getElementById($elementId)) !== null
            ? new AssertableHtmlElement($element)
            : null;
    }

    /** Return assertable elements matching the given tag. */
    public function getElementsByTagName(string $qualifiedName): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->document->getElementsByTagName($qualifiedName));
    }
}
