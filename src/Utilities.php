<?php

namespace Ziadoz\AssertableHtml;

use Dom\Document;
use Dom\HTMLDocument;
use Dom\HtmlElement;

class Utilities
{
    /** Return a simple selector for the given element (e.g. p#foo.bar.baz) */
    public static function selectorFromElement(HtmlElement $element): string
    {
        $parts = [mb_strtolower($element->tagName)];

        if (trim($id = $element->id) !== '') {
            $parts[] = '#' . self::normaliseWhitespace(trim($id));
        }

        if (count($element->classList) > 0) {
            foreach ($element->classList as $class) {
                $parts[] = '.' . $class;
            }
        }

        return implode('', $parts);
    }

    /**
     * Normalise the whitespace of the given string.
     *
     * @link: https://github.com/symfony/symfony/pull/48940
     */
    public static function normaliseWhitespace(string $string): string
    {
        return trim(preg_replace("/[ \n\r\t\x0C]{2,}+|[\n\r\t\x0C]/", ' ', $string), " \n\r\t\x0C");
    }
}
