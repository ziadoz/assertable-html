<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Mixins;

use Closure;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class TestComponentMixins
{
    /** Return an assertable HTML element. */
    public function assertableElement(): Closure
    {
        return function (int $options = LIBXML_HTML_NOIMPLIED, ?string $overrideEncoding = null): AssertableDocument {
            return AssertableDocument::createFromString((string) $this->rendered, $options, $overrideEncoding);
        };
    }

    /** Return an assertable HTML element. */
    public function assertElement(): Closure
    {
        return function (callable $callback, int $options = LIBXML_HTML_NOIMPLIED, ?string $overrideEncoding = null): static {
            $this->assertableElement($options, $overrideEncoding)->scope($callback);

            return $this;
        };
    }
}
