<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Support;

class Whitespace
{
    /** Normalise the whitespace of the given string. @link: https://github.com/symfony/symfony/pull/48940 */
    public static function normalise(string $string): string
    {
        return trim(preg_replace("/[ \n\r\t\x0C]{2,}+|[\n\r\t\x0C]/", ' ', $string), " \n\r\t\x0C");
    }
}
