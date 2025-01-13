<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Prototype\Concerns;

use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsClassList
{
    /*
    |--------------------------------------------------------------------------
    | Assert Class
    |--------------------------------------------------------------------------
    */

    /**
     * Assert the class list passes the given callback.
     *
     * @param  callable(static $classes): bool  $callback
     */
    public function assertClasses(callable $callback, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $callback($this),
            $message ?? "The class list doesn't pass the given callback.",
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Empty
    |--------------------------------------------------------------------------
    */

    /** Assert the class list is empty. */
    public function assertEmpty(): static
    {
        PHPUnit::assertTrue(
            $this->empty(),
            $message ?? "The class list isn't empty.",
        );

        return $this;
    }

    /** Assert the class list is not empty. */
    public function assertNotEmpty(): static
    {
        PHPUnit::assertFalse(
            $this->empty(),
            $message ?? "The class list isn't empty.",
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Contains
    |--------------------------------------------------------------------------
    */

    /** Assert the class list contains the given class. */
    public function assertContains(string $class, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $this->contains($class),
            $message ?? sprintf(
                "The class list doesn't contain the given class [%s].",
                $class,
            ),
        );

        return $this;
    }

    /** Assert the element's class doesn't contain the given class. */
    public function assertDoesntContain(string $class, ?string $message = null): static
    {
        PHPUnit::assertFalse(
            $this->contains($class),
            $message ?? sprintf(
                'The class list contains the given class [%s].',
                $class,
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Any/All
    |--------------------------------------------------------------------------
    */

    /** Assert the class list contains any of the given classes. */
    public function assertAny(array $classes, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $this->any($classes),
            $message ?? sprintf(
                "The class list doesn't contain any of the classes [%s]",
                implode(' ', $classes),
            ),
        );

        return $this;
    }

    /** Assert the class list contains all the given classes. */
    public function assertAll(array $classes, ?string $message = null): static
    {
        PHPUnit::assertTrue(
            $this->all($classes),
            $message ?? sprintf(
                "The class list doesn't contain all the classes [%s]",
                implode(' ', $classes),
            ),
        );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Value
    |--------------------------------------------------------------------------
    */

    /** Assert the class list value equals the given value. */
    public function assertValueEquals(string $value, ?string $message = null): static
    {
        PHPUnit::assertSame(
            $value,
            $this->value,
            $message ?? "The class list doesn't equal the given value.",
        );

        return $this;
    }

    /** Assert the class list value doesn't equal the given value. */
    public function assertValueDoesntEqual(string $value, ?string $message = null): static
    {
        PHPUnit::assertNotSame(
            $value,
            $this->value,
            $message ?? 'The class list equals the given value.',
        );

        return $this;
    }
}
