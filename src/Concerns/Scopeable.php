<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

trait Scopeable
{
    /**
     * Scope the current assertable, mostly for readability purposes.
     *
     * @param  callable(static $assertable): void  $callback
     */
    public function scope(callable $callback): static
    {
        $callback($this);

        return $this;
    }
}
