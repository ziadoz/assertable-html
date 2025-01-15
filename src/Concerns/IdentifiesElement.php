<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Dom\Document;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Support\Whitespace;

trait IdentifiesElement
{
    /** Return a simple identifying selector for the given element (e.g. p#foo.bar.baz) */
    public function identifier(): string
    {
        return implode('', array_filter([
            $this->formatTag($this->root),
            $this->formatId($this->root),
            $this->formatClasses($this->root),
        ]));
    }

    /** Return the element's formatted tag name. */
    private function formatTag(HTMLDocument|Document|HtmlElement|Element $element): string
    {
        return mb_strtolower($element->tagName);
    }

    /** Return the element's formatted ID (if applicable). */
    private function formatId(HTMLDocument|Document|HtmlElement|Element $element): string
    {
        return trim($id = $element->id) !== '' ? '#' . Whitespace::normaliseWhitespace(trim($id)) : '';
    }

    /** Return the elements format classes (if any). */
    private function formatClasses(HTMLDocument|Document|HtmlElement|Element $element): string
    {
        return implode('', array_map(fn (string $class): string => '.' . $class, iterator_to_array($element->classList)));
    }
}
