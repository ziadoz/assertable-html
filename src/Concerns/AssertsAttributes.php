<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsAttributes
{
    /*
    |--------------------------------------------------------------------------
    | Assert Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the attribute list passes the given callback.
     *
     * @param  callable(static $attributes): bool  $callback
     */
    public function assertAttributes(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this),
            $message ?? "The attribute list doesn't pass the given callback.",
        );

        return $this;
    }

    /**
     * Assert the attribute list passes the given callback.
     *
     * @param  callable(?string $value): bool  $callback
     */
    public function assertAttribute(string $attribute, callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this->attributes[$attribute] ?? null),
            $message ?? sprintf(
                "The attribute [%s] doesn't pass the given callback.",
                $attribute,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Empty
    |--------------------------------------------------------------------------
    */

    /** Assert the attribute list is empty. */
    public function assertEmpty(?string $message = null): static
    {
        PHPUnit::assertTrue(
            $this->empty(),
            $message ?? "The attribute list isn't empty.",
        );

        return $this;
    }

    /** Assert the attribute list is not empty. */
    public function assertNotEmpty(?string $message = null): static
    {
        PHPUnit::assertFalse(
            $this->empty(),
            $message ?? 'The attribute list is empty.',
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Present / Missing
    |--------------------------------------------------------------------------
    */

    /** Assert the given attribute is present in the attribute list. */
    public function assertPresent(string $attribute, ?string $message = null): static
    {
        PHPUnit::assertArrayHasKey(
            $attribute,
            $this->attributes,
            $message ?? sprintf(
                "The attribute list doesn't contain the [%s] attribute.",
                $attribute,
            ),
        );

        return $this;
    }

    /** Assert the given attribute is missing in the attribute list. */
    public function assertMissing(string $attribute, ?string $message = null): static
    {
        PHPUnit::assertArrayNotHasKey(
            $attribute,
            $this->attributes,
            $message ?? sprintf(
                'The attribute list contains the [%s] attribute.',
                $attribute,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Equals
    |--------------------------------------------------------------------------
    */

    /** Assert the given attribute equals the given value in the attribute list. */
    public function assertEquals(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $value,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The attribute [%s] doesn't equal the given value [%s].",
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /** Assert the given attribute doesn't equal the given value in the attribute list. */
    public function assertDoesntEqual(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertNotSame(
            $value,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The attribute [%s] equals the given value [%s].',
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Starts / Ends With
    |--------------------------------------------------------------------------
    */

    /** Assert the given attribute starts with the given prefix in the attribute list. */
    public function assertStartsWith(string $attribute, string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringStartsWith(
            $prefix,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The attribute [%s] doesn't start with the given prefix [%s].",
                $attribute,
                $prefix,
            ),
        );

        return $this;
    }

    /** Assert the given attribute doesn't start with the given prefix in the attribute list. */
    public function assertDoesntStartWith(string $attribute, string $prefix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringStartsNotWith(
            $prefix,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The attribute [%s] starts with the given prefix [%s].',
                $attribute,
                $prefix,
            ),
        );

        return $this;
    }

    /** Assert the given attribute ends with the given suffix in the attribute list. */
    public function assertEndsWith(string $attribute, string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringEndsWith(
            $suffix,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The attribute [%s] doesn't end with the given suffix [%s].",
                $attribute,
                $suffix,
            ),
        );

        return $this;
    }

    /** Assert the given attribute doesn't end with the given suffix in the attribute list. */
    public function assertDoesntEndWith(string $attribute, string $suffix, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringStartsNotWith(
            $suffix,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The attribute [%s] ends with the given suffix [%s].',
                $attribute,
                $suffix,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the given attribute contains the given value in the attribute list. */
    public function assertContains(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringContainsString(
            $value,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                "The attribute [%s] doesn't contains the given value [%s].",
                $attribute,
                $value,
            ),
        );

        return $this;
    }

    /** Assert the given attribute doesn't contain the given value in the attribute list. */
    public function assertDoesntContain(string $attribute, string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        PHPUnit::assertStringNotContainsString(
            $value,
            $this->value($attribute, $normaliseWhitespace),
            $message ?? sprintf(
                'The attribute [%s] contains the given value [%s].',
                $attribute,
                $value,
            ),
        );

        return $this;
    }
}
