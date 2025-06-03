<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Dom\Document;
use Dom\HTMLDocument;
use ErrorException;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Concerns\AssertsDocument;
use Ziadoz\AssertableHtml\Concerns\Scopeable;
use Ziadoz\AssertableHtml\Concerns\Whenable;
use Ziadoz\AssertableHtml\Concerns\Findable;
use Ziadoz\AssertableHtml\Exceptions\UnableToCreateAssertableDocument;

final readonly class AssertableDocument
{
    use AssertsDocument;
    use Scopeable;
    use Whenable;
    use Findable;

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
        return self::promoteErrorsToExceptions(fn () => new self(HTMLDocument::createFromFile($path, $options, $overrideEncoding)));
    }

    /** Create an assertable document from a string. */
    public static function createFromString(string $source, int $options = 0, ?string $overrideEncoding = null): self
    {
        return self::promoteErrorsToExceptions(fn () => new self(HTMLDocument::createFromString($source, $options, $overrideEncoding)));
    }

    /** Return the assertable element matching the given selector. */
    public function querySelector(string $selector): AssertableElement
    {
        if (($element = $this->document->querySelector($selector)) === null) {
            PHPUnit::fail(sprintf(
                "The document doesn't contain an element matching the given selector [%s].",
                $selector,
            ));
        }

        return new AssertableElement($element);
    }

    /** Return assertable elements matching the given selector. */
    public function querySelectorAll(string $selector): AssertableElementsList
    {
        return new AssertableElementsList($this->document->querySelectorAll($selector));
    }

    /** Return an assertable element matching the given ID. */
    public function getElementById(string $id): AssertableElement
    {
        if (($element = $this->document->getElementById($id)) === null) {
            PHPUnit::fail(sprintf(
                "The document doesn't contain an element matching the given ID [%s].",
                $id,
            ));
        }

        return new AssertableElement($element);
    }

    /** Return assertable elements matching the given tag. */
    public function getElementsByTagName(string $tag): AssertableElementsList
    {
        return new AssertableElementsList($this->document->getElementsByTagName($tag));
    }

    /** Promote any PHP errors that occur in the given callback to custom exceptions. */
    private static function promoteErrorsToExceptions(callable $callback): mixed
    {
        try {
            set_error_handler(function (int $severity, string $message, string $file, int $line): never {
                throw new UnableToCreateAssertableDocument(
                    'Unable to create assertable HTML document.',
                    previous: new ErrorException($message, $severity, $severity, $file, $line),
                );
            });

            return $callback();
        } finally {
            restore_error_handler();
        }
    }
}
