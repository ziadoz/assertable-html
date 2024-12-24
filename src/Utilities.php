<?php

namespace Ziadoz\AssertableHtml;

use Dom\HtmlElement;
use Dom\NodeList;

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
