<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsText
{
    /*
    |--------------------------------------------------------------------------
    | Assert Text
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the text passes the given callback.
     *
     * @param  callable(string $text): bool  $callback
     */
    public function assertText(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this),
            $message ?? "The text doesn't pass the given callback.",
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the text equals the given text. */
    public function assertEquals(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $text,
            $this->value($normaliseWhitespace),
            $message ?? "The text doesn't equal the given text.",
        );

        return $this;
    }

    /** Assert the text doesn't equal the given text. */
    public function assertDoesntEqual(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertNotSame(
            $text,
            $this->value($normaliseWhitespace),
            $message ?? 'The text equals the given text.',
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Starts / Ends With
    |--------------------------------------------------------------------------
    */

    /** Assert the text starts with the given text. */
    public function assertStartsWith(string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringStartsWith(
            $prefix,
            $this->value($normaliseWhitespace),
            $message ?? "The text doesn't start with the given prefix text.",
        );

        return $this;
    }

    /** Assert the text starts doesn't start with the given text. */
    public function assertDoesntStartWith(string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringStartsNotWith(
            $prefix,
            $this->value($normaliseWhitespace),
            $message ?? 'The text starts with the given prefix text.',
        );

        return $this;
    }

    /** Assert the text ends with the given text. */
    public function assertEndsWith(string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringEndsWith(
            $suffix,
            $this->value($normaliseWhitespace),
            $message ?? "The text doesn't end with the given suffix text.",
        );

        return $this;
    }

    /** Assert the text doesn't end with the given text. */
    public function assertDoesntEndWith(string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringEndsNotWith(
            $suffix,
            $this->value($normaliseWhitespace),
            $message ?? 'The text ends with the given suffix text.',
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the text contains the given text. */
    public function assertContains(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringContainsString(
            $text,
            $this->value($normaliseWhitespace),
            $message ?? "The text doesn't contain the given text.",
        );

        return $this;
    }

    /** Alias for assertTextContains() */
    public function assertSeeIn(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertContains($text, $normaliseWhitespace, $message);

        return $this;
    }

    /** Assert the text doesn't contain the given text. */
    public function assertDoesntContain(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringNotContainsString(
            $text,
            $this->value($normaliseWhitespace),
            $message ?? 'The element [%s] text contains the given text.',
        );

        return $this;
    }

    /** Alias for assertTextDoesntContain() */
    public function assertDontSeeIn(string $text, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->assertDoesntContain($text, $normaliseWhitespace, $message);

        return $this;
    }
}
