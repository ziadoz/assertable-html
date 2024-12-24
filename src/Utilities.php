<?php

namespace Ziadoz\AssertableHtml;

use Dom\Document;
use Dom\HTMLDocument;
use Dom\HtmlElement;
use Dom\NodeList;

class Utilities
{
    /** Return a simple selector for the given element (e.g. p#foo.bar.baz) */
    public static function selectorFromElement(HtmlElement $element): string
    {
        return implode('', array_filter([
            self::formatTag($element),
            self::formatId($element),
            self::formatClasses($element),
        ]));
    }

    public static function formatTag(HTMLDocument|Document|HtmlElement $element): string
    {
        return mb_strtolower($element->tagName);
    }

    public static function formatId(HTMLDocument|Document|HtmlElement $element): string
    {
        return trim($id = $element->id) !== '' ? '#' . self::normaliseWhitespace(trim($id)) : '';
    }

    public static function formatClasses(HTMLDocument|Document|HtmlElement $element): string
    {
        $formatted = '';

        foreach ($element->classList as $class) {
            $formatted .= '.' . $class;
        }

        return $formatted;
    }

    /** Normalise the whitespace of the given string. @link: https://github.com/symfony/symfony/pull/48940 */
    public static function normaliseWhitespace(string $string): string
    {
        return trim(preg_replace("/[ \n\r\t\x0C]{2,}+|[\n\r\t\x0C]/", ' ', $string), " \n\r\t\x0C");
    }

    /** Convert a slice of a NodeList to an HTML output. */
    public static function nodesToMatchesHtml(NodeList $nodes, ?int $limit = 3): string
    {
        $total = count($nodes);

        if ($total === 0) {
            return '';
        }

        $nodes = $limit ? array_slice(iterator_to_array($nodes), 0, $limit) : $nodes;

        /** @link: https://evertpot.com/222/ */
        $format = function (string $html): string {
            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $html);
            rewind($stream);

            $formatted = '';

            while (($line = fgets($stream)) !== false) {
                $formatted .= '> ' . $line;
            }

            return $formatted;
        };

        $html = $total . " Matching Element(s) Found\n";
        $html .= str_repeat('=', strlen($html));
        $html .= "\n\n";

        foreach ($nodes as $index => $node) {
            $html .= sprintf("%d. [%s]:\n", $index + 1, self::selectorFromElement($node));
            $html .= $format($node->ownerDocument->saveHtml($node));
            $html .= "\n\n";
        }

        return trim($html) . ($limit ? "\n\n" . ($total - $limit) . ' Matching Elements Omitted...' : '');
    }
}
