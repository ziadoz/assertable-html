<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Dom\Element;
use Ziadoz\AssertableHtml\Support\Whitespace;

trait IdentifiesElement
{
    /** Return a simple identifying selector for the given element (e.g. p#foo.bar.baz) */
    public function identifier(): string
    {
        return implode('', array_filter([
            $this->formatTag(),
            $this->formatId(),
            $this->formatClasses(),
        ]));
    }

    /** Return the element's formatted tag name. */
    private function formatTag(): string
    {
        return mb_strtolower($this->element->tagName);
    }

    /** Return the element's formatted ID (if applicable). */
    private function formatId(): string
    {
        return trim($id = $this->element->id) !== '' ? '#' . Whitespace::normalise(trim($id)) : '';
    }

    /** Return the elements format classes (if any). */
    private function formatClasses(): string
    {
        return implode('', array_map(fn (string $class): string => '.' . $class, iterator_to_array($this->element->classList)));
    }
}
