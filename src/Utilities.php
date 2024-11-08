<?php
namespace Ziadoz\AssertableHtml;

use Dom\HtmlElement;

class Utilities
{
    public static function selectorFromElement(HtmlElement $element): string
    {
        $parts = [mb_strtolower($element->tagName)];

        if (trim((string) $id = $element->getAttribute('id')) !== '') {
            $parts[] = '#' . self::normaliseWhitespace(trim($id));
        }

        if (count($element->classList) > 0) {
            $class = [];

            for ($i = 0; $i < count($element->classList); $i++) {
                $class[] = $element->classList->item($i);
            }

            $parts[] = '.' . implode('.', $class);
        }

        return implode('', $parts);
    }

    // @see: https://github.com/symfony/symfony/pull/48940
    public static function normaliseWhitespace(string $string): string
    {
        return trim(preg_replace("/(?:[ \n\r\t\x0C]{2,}+|[\n\r\t\x0C])/", ' ', $string), " \n\r\t\x0C");
    }
}
