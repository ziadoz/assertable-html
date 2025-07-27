<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use PHPUnit\Framework\AssertionFailedError;

trait AssertsMany
{
    /** Perform many PHPUnit assertions in a callback, but capture any failures into a single exception. */
    public function assertMany(callable $callback, ?string $message = null): static
    {
        try {
            $callback(...)->call($this);
        } catch (AssertionFailedError $assertion) {
            throw new AssertionFailedError(message: $message ?? $assertion->getMessage(), previous: $assertion);
        }

        return $this;
    }
}
