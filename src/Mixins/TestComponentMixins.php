<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Mixins;

use Closure;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class TestComponentMixins
{
    /** Return an assertable HTML element of the component. */
    public function assertableHtml(): Closure
    {
        return function (int $options = LIBXML_HTML_NOIMPLIED, ?string $overrideEncoding = null): AssertableDocument {
            return AssertableDocument::createFromString((string) $this, $options, $overrideEncoding);
        };
    }

    /** Return an assertable HTML element scoped to the component. */
    public function assertComponent(): Closure
    {
        return function (callable $callback, int $options = LIBXML_HTML_NOIMPLIED, ?string $overrideEncoding = null): static {
            $this->assertableHtml($options, $overrideEncoding)->with($callback);

            return $this;
        };
    }

    /** Return an assertable HTML element scoped to the given selector in the component. */
    public function assertElement(): Closure
    {
        return function (string $selector, callable $callback, int $options = LIBXML_HTML_NOIMPLIED, ?string $overrideEncoding = null): static {
            $this->assertableHtml($options, $overrideEncoding)->one($selector, $callback);

            return $this;
        };
    }
}
