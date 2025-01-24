<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Document;
use Dom\HTMLDocument;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Concerns\AssertsDocument;
use Ziadoz\AssertableHtml\Concerns\Scopeable;
use Ziadoz\AssertableHtml\Concerns\Whenable;
use Ziadoz\AssertableHtml\Concerns\Withable;

final readonly class AssertableDocument
{
    use AssertsDocument;
    use Scopeable;
    use Whenable;
    use Withable;

    /** The document's page title. */
    public string $title;

    /** Create a new assertable document. */
    public function __construct(private HTMLDocument|Document $document)
    {
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
    | Native
    |--------------------------------------------------------------------------
    */

    /** Create an assertable document from a file. */
    public static function createFromFile(string $path, int $options = 0, ?string $overrideEncoding = null): self
    {
        return new self(HTMLDocument::createFromFile($path, $options, $overrideEncoding));
    }

    /** Create an assertable document from a string. */
    public static function createFromString(string $source, int $options = 0, ?string $overrideEncoding = null): self
    {
        return new self(HTMLDocument::createFromString($source, $options, $overrideEncoding));
    }

    /** Create an assertable document from an existing HTML document. */
    public static function createFromDocument(HTMLDocument|Document $document): self
    {
        return new self(clone $document);
    }

    /** Return the assertable element matching the given selectors. */
    public function querySelector(string $selectors): AssertableElement
    {
        if (($element = $this->document->querySelector($selectors)) === null) {
            PHPUnit::fail(sprintf(
                "The document doesn't contain an element matching the given selectors [%s].",
                $selectors,
            ));
        }

        return new AssertableElement($element);
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
