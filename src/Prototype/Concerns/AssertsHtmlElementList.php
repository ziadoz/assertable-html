<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype\Concerns;

use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsHtmlElementList
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

    /** Assert the element list contains the given number of elements. */
    public function assertCount(int $expected, ?string $message = null): static
    {
        PHPUnit::assertCount(
            $expected,
            $this,
            $message ?? sprintf(
                "The element list doesn't contain exactly [%d] elements.",
                $expected,
            ),
        );

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
