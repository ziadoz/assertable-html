<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

trait Withable
{
    /**
     * Scope the first assertable element within the current assertable document or element matching the given selector.
     *
     * @param  callable(?AssertableElement $assertable): void  $callback
     */
    public function with(string $selector, callable $callback): static
    {
        $callback($this->querySelector($selector));

        return $this;
    }

    /**
     * Scope the first assertable element elsewhere in the assertable document matching the given selector.
     *
     * @param  callable(?AssertableElement $assertable): void  $callback
     */
    public function elsewhere(string $selector, callable $callback): static
    {
        $document = $this instanceof AssertableDocument ? $this : $this->document;

        $callback($document->querySelector($selector));

        return $this;
    }
}
