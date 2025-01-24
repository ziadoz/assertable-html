<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElementsList;

trait Withable
{
    /**
     * Scope the first assertable element within the current assertable document or element matching the given selector.
     *
     * @param  callable(AssertableElement $assertable): void  $callback
     */
    public function with(string $selector, callable $callback): static
    {
        $callback($this->querySelector($selector));

        return $this;
    }

    /**
     * Scope the matching assertable elements within the current assertable document or element matching the given selector.
     *
     * @param  callable(AssertableElementsList $assertable): void  $callback
     */
    public function many(string $selector, callable $callback): static
    {
        $callback($this->querySelectorAll($selector));

        return $this;
    }

    /**
     * Scope the first assertable element elsewhere in the assertable document matching the given selector.
     *
     * @param  callable(AssertableElement $assertable): void  $callback
     */
    public function elsewhere(string $selector, callable $callback): static
    {
        $document = $this instanceof AssertableDocument
            ? $this
            : AssertableDocument::createFromDocument($this->element->ownerDocument);

        $callback($document->querySelector($selector));

        return $this;
    }
}
