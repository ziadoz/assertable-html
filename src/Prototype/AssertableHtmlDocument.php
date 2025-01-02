<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype;

use Dom\Document;
use Dom\HTMLDocument;

readonly class AssertableHtmlDocument
{
    private function __construct(private HTMLDocument|Document $document)
    {
    }

    public static function createEmpty(string $encoding = 'UTF-8'): static
    {
        return new static(HTMLDocument::createEmpty());
    }

    public static function createFromFile(string $path, int $options = 0, ?string $overrideEncoding = null): static
    {
        return new static(HTMLDocument::createFromFile($path, $options, $overrideEncoding));
    }

    public static function createFromString(string $source, int $options = 0, ?string $overrideEncoding = null): static
    {
        return new static(HTMLDocument::createFromString($source, $options, $overrideEncoding));
    }

    public function saveHtml(?AssertableHtmlElement $element = null): string
    {
        return $this->document->saveHtml($element);
    }

    public function querySelector(string $selectors): ?AssertableHtmlElement
    {
        return new AssertableHtmlElement($this->document->querySelector($selectors));
    }

    public function querySelectorAll(string $selectors): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->document->querySelectorAll($selectors));
    }

    public function getElementById(string $elementId): ?AssertableHtmlElement
    {
        return ($element = $this->document->getElementById($elementId)) !== null
            ? new AssertableHtmlElement($element)
            : null;
    }

    public function getElementsByTagName(string $qualifiedName): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->document->getElementsByTagName($qualifiedName));
    }
}
