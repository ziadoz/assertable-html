<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsElementsList
{
    /*
    |--------------------------------------------------------------------------
    | Assert Elements
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element list passes the given callback.
     *
     * @param  callable(static $element): bool  $callback
     */
    public function assertElements(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this),
            $message ?? "The element list doesn't pass the given callback.",
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Empty
    |--------------------------------------------------------------------------
    */

    /** Assert the element list is empty. */
    public function assertEmpty(?string $message = null): static
    {
        PHPUnit::assertTrue(
            $this->empty(),
            $message ?? "The element list isn't empty.",
        );

        return $this;
    }

    /** Assert the element list isn't empty. */
    public function assertNotEmpty(?string $message = null): static
    {
        PHPUnit::assertFalse(
            $this->empty(),
            $message ?? 'The element list is empty.',
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Count
    |--------------------------------------------------------------------------
    */

    /** Assert the element list contains the given number of elements. */
    public function assertCount(int $count, ?string $message = null): static
    {
        $this->countNotNegative($count);

        PHPUnit::assertCount($count, $this, $message ?? sprintf(
            "The element list doesn't have exactly [%d] elements.",
            $count,
        ));

        return $this;
    }

    /** Assert the element list contains greater than the given number of elements. */
    public function assertCountGreaterThan(int $count, ?string $message = null): static
    {
        $this->countNotNegative($count);

        PHPUnit::assertGreaterThan($count, count($this), $message ?? sprintf(
            "The element list doesn't have greater than [%d] elements.",
            $count,
        ));

        return $this;
    }

    /** Assert the element list contains greater than or equal the given number of elements. */
    public function assertCountGreaterThanOrEqual(int $count, ?string $message = null): static
    {
        $this->countNotNegative($count);

        PHPUnit::assertGreaterThanOrEqual($count, count($this), $message ?? sprintf(
            "The element list doesn't have greater than or equal to [%d] elements.",
            $count,
        ));

        return $this;
    }

    /** Assert the element list contains less than the given number of elements. */
    public function assertCountLessThan(int $count, ?string $message = null): static
    {
        $this->countNotNegative($count);

        PHPUnit::assertLessThan($count, count($this), $message ?? sprintf(
            "The element list doesn't have less than [%d] elements.",
            $count,
        ));

        return $this;
    }

    /** Assert the element contains less than or equal the given number of elements. */
    public function assertCountLessThanOrEqual(int $count, ?string $message = null): static
    {
        $this->countNotNegative($count);

        PHPUnit::assertLessThanOrEqual($count, count($this), $message ?? sprintf(
            "The element list doesn't have less than or equal to [%d] elements.",
            $count,
        ));

        return $this;
    }

    /** Ensure the given count value is not negative, which makes no sense. */
    private function countNotNegative(int $count): void
    {
        if ($count < 0) {
            PHPUnit::fail('Expected number of elements in a list cannot be less than zero.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Any / All
    |--------------------------------------------------------------------------
    */

    /** Assert any element in the element list passes the given callback. */
    public function assertAny(?callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            array_any(iterator_to_array($this), $callback),
            $message ?? 'No elements in the list match the given callback.',
        );

        return $this;
    }

    /** Assert all elements in the element list pass the given callback. */
    public function assertAll(?callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            array_all(iterator_to_array($this), $callback),
            $message ?? 'Not every element in the list matches the given callback.',
        );

        return $this;
    }
}
