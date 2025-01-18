<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsElementList
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
    | Assert Count
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the element list contains the expected number of elements.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     */
    public function assertNumberOfElements(string $comparison, int $count, ?string $message = null): static
    {
        if ($count < 0) {
            throw new InvalidArgumentException('Expected number of elements in a list cannot be less than zero');
        }

        $message ??= sprintf(
            "The element list doesn't have %s [%d] elements.",
            match ($comparison) {
                '='     => 'exactly',
                '>'     => 'greater than',
                '>='    => 'greater than or equal to',
                '<'     => 'less than',
                '<='    => 'less than or equal to',
                default => throw new OutOfBoundsException('Invalid comparison operator: ' . $comparison),
            },
            $count,
        );

        match ($comparison) {
            '='     => PHPUnit::assertCount($count, $this, $message),
            '>'     => PHPUnit::assertGreaterThan($count, count($this), $message),
            '>='    => PHPUnit::assertGreaterThanOrEqual($count, count($this), $message),
            '<'     => PHPUnit::assertLessThan($count, count($this), $message),
            '<='    => PHPUnit::assertLessThanOrEqual($count, count($this), $message),
            default => throw new OutOfBoundsException('Invalid comparison operator: ' . $comparison),
        };

        return $this;
    }

    /** Assert the element list contains the given number of elements. */
    public function assertCount(int $count, ?string $message = null): static
    {
        $this->assertNumberOfElements('=', $count, $message);

        return $this;
    }

    /** Assert the element list contains greater than the given number of elements. */
    public function assertGreaterThan(int $count, ?string $message = null): static
    {
        $this->assertNumberOfElements('>', $count, $message);

        return $this;
    }

    /** Assert the element list contains greater than or equal the given number of elements. */
    public function assertGreaterThanOrEqual(int $count, ?string $message = null): static
    {
        $this->assertNumberOfElements('>=', $count, $message);

        return $this;
    }

    /** Assert the element list contains less than the given number of elements. */
    public function assertLessThan(int $count, ?string $message = null): static
    {
        $this->assertNumberOfElements('<', $count, $message);

        return $this;
    }

    /** Assert the element contains less than or equal the given number of elements. */
    public function assertLessThanOrEqual(int $count, ?string $message = null): static
    {
        $this->assertNumberOfElements('<=', $count, $message);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Any/All
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
