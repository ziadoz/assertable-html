<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Ziadoz\AssertableHtml\Dom\AssertableHtmlElement;

trait Withable
{
    /**
     * Scope the first assertable element within the current assertable document or element matching the given selector.
     *
     * @param  callable(?AssertableHtmlElement $assertable): bool|mixed  $callback
     */
    public function with(string $selector, ?callable $callback): ?AssertableHtmlElement
    {
        $element = $this->querySelector($selector);

        if ($callback) {
            $callback($element);
        }

        return $element;
    }

    /**
     * Scope the first assertable element elsewhere in the assertable document matching the given selector.
     *
     * @param  callable(?AssertableHtmlElement $assertable): bool|mixed  $callback
     */
    public function elsewhere(string $selector, ?callable $callback): ?AssertableHtmlElement
    {
        $element = $this->ownerDocument->querySelector($selector);

        if ($callback) {
            $callback($element);
        }

        return $element;
    }
}
