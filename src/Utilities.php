<?php

namespace Ziadoz\AssertableHtml;

use Dom\HtmlElement;

class Utilities
{
    public static function selectorFromElement(HtmlElement $element): string
    {
        $parts = [mb_strtolower($element->tagName)];

        if (trim($id = $element->id) !== '') {
            $parts[] = '#' . self::normaliseWhitespace(trim($id));
        }

        if (count($element->classList) > 0) {
            $parts = array_merge($parts, array_map(
                fn (string $class): string => '.' . $class,
                iterator_to_array($element->classList->getIterator()),
            ));
        }

        return implode('', $parts);
    }

    // @see: https://github.com/symfony/symfony/pull/48940
    public static function normaliseWhitespace(string $string): string
    {
        return trim(preg_replace("/[ \n\r\t\x0C]{2,}+|[\n\r\t\x0C]/", ' ', $string), " \n\r\t\x0C");
    }
}
