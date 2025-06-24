<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

trait Scopeable
{
    /**
     * Scope with the current assertable, mostly for readability purposes.
     *
     * @param  callable(static $assertable): void  $callback
     */
    public function with(callable $callback): static
    {
        $callback($this);

        return $this;
    }
}
