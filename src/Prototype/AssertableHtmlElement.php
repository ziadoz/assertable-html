<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Concerns\AssertsHtml;
use Ziadoz\AssertableHtml\Support\Utilities;

readonly class AssertableHtmlElement
{
    use AssertsHtml;

    public string $tagName;
    public string $className;
    public string $id;
    public string $textContent;
    public string $normalisedTextContent;

    public function __construct(private HTMLElement|Element $root)
    {
        dump($this->root->prefix);
        dump($this->root->localName);

        $this->tagName = $this->root->tagName;
        $this->className = $this->root->className;
        $this->id = $this->root->id;
        $this->textContent = $this->root->textContent;
        $this->normalisedTextContent = Utilities::normaliseWhitespace($this->root->textContent);
        // classList wrapper with assertions, classes array.
    }

    public function querySelector(string $selectors): ?AssertableHtmlElement
    {
        return new AssertableHtmlElement($this->root->querySelector($selectors));
    }

    public function querySelectorAll(string $selectors): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->root->querySelectorAll($selectors));
    }

    public function getElementsByTagName(string $qualifiedName): AssertableHtmlElementsList
    {
        return new AssertableHtmlElementsList($this->root->getElementsByTagName($qualifiedName));
    }
}
