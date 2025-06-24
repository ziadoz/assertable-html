<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom;

use Stringable;
use Ziadoz\AssertableHtml\Concerns\Asserts\AssertsText;
use Ziadoz\AssertableHtml\Support\Whitespace;

final readonly class AssertableText implements Stringable
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
            ? Whitespace::normalise($this->text)
            : $this->text;
    }

    /** Return the assertable text. */
    public function __toString(): string
    {
        return $this->text;
    }
}
