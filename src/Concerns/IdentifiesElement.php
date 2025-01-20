<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Support\Whitespace;

trait IdentifiesElement
{
    /** Return a simple identifying selector for the given element (e.g. p#foo.bar.baz) */
    public function identifier(): string
    {
        return implode('', array_filter([
            $this->formatTag($this->element),
            $this->formatId($this->element),
            $this->formatClasses($this->element),
        ]));
    }

    /** Return the element's formatted tag name. */
    private function formatTag(HtmlElement|Element $element): string
    {
        return mb_strtolower($element->tagName);
    }

    /** Return the element's formatted ID (if applicable). */
    private function formatId(HtmlElement|Element $element): string
    {
        return trim($id = $element->id) !== '' ? '#' . Whitespace::normalise(trim($id)) : '';
    }

    /** Return the elements format classes (if any). */
    private function formatClasses(HtmlElement|Element $element): string
    {
        return implode('', array_map(fn (string $class): string => '.' . $class, iterator_to_array($element->classList)));
    }
}
