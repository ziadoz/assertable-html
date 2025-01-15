<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype\Concerns;

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

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Contains
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Within
    |--------------------------------------------------------------------------
    */
}
