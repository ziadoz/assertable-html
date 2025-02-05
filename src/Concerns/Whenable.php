<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

trait Whenable
{
    /**
     * Return a value if the given condition is true, otherwise return a default.
     *
     * @param  callable(static $assertable): bool|bool  $condition
     * @param  callable(static $assertable): callable  $callback
     * @param  ?callable(static $assertable): ?callable  $default
     */
    public function when(callable|bool $condition, callable $callback, ?callable $default = null): static
    {
        $condition = is_callable($condition) ? $condition($this) : $condition;

        if ($condition) {
            $callback($this);
        } elseif ($default) {
            $default($this);
        }

        return $this;
    }
}
