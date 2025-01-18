<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Ziadoz\AssertableHtml\Dom\AssertableHtmlDocument;
use Ziadoz\AssertableHtml\Dom\AssertableHtmlElement;

trait Withable
{
    /**
     * Scope the first assertable element within the current assertable document or element matching the given selector.
     *
     * @param  callable(?AssertableHtmlElement $assertable): bool|mixed  $callback
     */
    public function with(string $selector, callable $callback): static
    {
        $callback($this->querySelector($selector));

        return $this;
    }

    /**
     * Scope the first assertable element elsewhere in the assertable document matching the given selector.
     *
     * @param  callable(?AssertableHtmlElement $assertable): bool|mixed  $callback
     */
    public function elsewhere(string $selector, callable $callback): static
    {
        $document = $this instanceof AssertableHtmlDocument ? $this : $this->document;

        $callback($document->querySelector($selector));

        return $this;
    }
}
