<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElementsList;

trait Findable
{
    /**
     * Scope the first assertable element within the current assertable document or element matching the given selector.
     *
     * @param  callable(AssertableElement $assertable): void  $callback
     */
    public function find(string $selector, callable $callback): static
    {
        $callback($this->querySelector($selector));

        return $this;
    }

    /**
     * Scope the matching assertable elements within the current assertable document or element matching the given selector.
     *
     * @param  callable(AssertableElementsList $assertable): void  $callback
     */
    public function findMany(string $selector, callable $callback): static
    {
        $callback($this->querySelectorAll($selector));

        return $this;
    }

    /**
     * Scope the first assertable element elsewhere in the assertable document matching the given selector.
     *
     * @param  callable(AssertableElement $assertable): void  $callback
     */
    public function findElsewhere(string $selector, callable $callback): static
    {
        $document = $this instanceof AssertableDocument
            ? $this
            : AssertableDocument::createFromString($this->element->ownerDocument->saveHtml());

        $callback($document->querySelector($selector));

        return $this;
    }
}
