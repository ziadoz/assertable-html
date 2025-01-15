<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Stringable;
use Ziadoz\AssertableHtml\Concerns\AssertsText;
use Ziadoz\AssertableHtml\Support\Whitespace;

readonly class AssertableText implements Stringable
{
    use AssertsText;

    /** Create assertable text. */
    public function __construct(private string $text)
    {
    }

    /** Dump the assertable text. */
    public function dump(): void
    {
        dump($this->text);
    }

    /** Dump and die the assertable text. */
    public function dd(): never
    {
        dd($this->text);
    }

    /** Return the text (optionally whitespace normalised). */
    public function value(bool $normaliseWhitespace = false): string
    {
        return $normaliseWhitespace
            ? Whitespace::normaliseWhitespace($this->text)
            : $this->text;
    }

    /** Return whether the assertable text starts with the given text. */
    public function startsWith(string $prefix, bool $normaliseWhitespace = false): bool
    {
        return str_starts_with($this->value($normaliseWhitespace), $prefix);
    }

    /** Return whether the assertable text ends with the given text. */
    public function endsWith(string $suffix, bool $normaliseWhitespace = false): bool
    {
        return str_ends_with($this->value($normaliseWhitespace), $suffix);
    }

    /** Return whether the assertable text contains with the given text. */
    public function contains(string $contains, bool $normaliseWhitespace = false): bool
    {
        return str_contains($this->value($normaliseWhitespace), $contains);
    }

    /** Return the assertable text. */
    public function __toString(): string
    {
        return $this->text;
    }
}
