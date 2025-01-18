<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Document;
use Dom\HTMLDocument;
use ReflectionClass;
use Ziadoz\AssertableHtml\Concerns\Scopeable;
use Ziadoz\AssertableHtml\Concerns\Whenable;
use Ziadoz\AssertableHtml\Concerns\Withable;

final readonly class AssertableDocument
{
    use Scopeable;
    use Whenable;
    use Withable;

    /** The document's head. */
    public ?AssertableElement $head;

    /** The document's body. */
    public ?AssertableElement $body;

    /** The document's page title. */
    public string $title;

    /** Create a new assertable document. */
    public function __construct(private HTMLDocument|Document $document)
    {
        $this->head = AssertableElement::proxy($this->document->head);
        $this->body = AssertableElement::proxy($this->document->body);
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

    /*
    |--------------------------------------------------------------------------
    | Proxy
    |--------------------------------------------------------------------------
    */

    /** Create a lazy proxy assertable element for the given element. */
    public static function proxy(HTMLDocument|Document $document): self
    {
        return new ReflectionClass(self::class)->newLazyProxy(fn () => new self($document));
    }

    /*
    |--------------------------------------------------------------------------
    | Native
    |--------------------------------------------------------------------------
    */

    /** Create an assertable document from a file. */
    public static function createFromFile(string $path, int $options = 0, ?string $overrideEncoding = null): self
    {
        return new static(HTMLDocument::createFromFile($path, $options, $overrideEncoding));
    }

    /** Create an assertable document from a string. */
    public static function createFromString(string $source, int $options = 0, ?string $overrideEncoding = null): self
    {
        return new static(HTMLDocument::createFromString($source, $options, $overrideEncoding));
    }

    /** Return the assertable element matching the given selectors. */
    public function querySelector(string $selectors): ?AssertableElement
    {
        return ($element = $this->document->querySelector($selectors)) !== null
            ? new AssertableElement($element)
            : null;
    }

    /** Return assertable elements matching the given selectors. */
    public function querySelectorAll(string $selectors): AssertableElementsList
    {
        return new AssertableElementsList($this->document->querySelectorAll($selectors));
    }

    /** Return an assertable element matching the given ID. */
    public function getElementById(string $id): ?AssertableElement
    {
        return ($element = $this->document->getElementById($id)) !== null
            ? new AssertableElement($element)
            : null;
    }

    /** Return assertable elements matching the given tag. */
    public function getElementsByTagName(string $tag): AssertableElementsList
    {
        return new AssertableElementsList($this->document->getElementsByTagName($tag));
    }
}
