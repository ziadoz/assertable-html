<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Mixins;

use Closure;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

class TestViewMixins
{
    /** Return an assertable HTML element. */
    public function assertableElement(): Closure
    {
        return function (int $options = 0, ?string $overrideEncoding = null): AssertableElement {
            return AssertableDocument::createFromString((string) $this, $options | LIBXML_NOERROR, $overrideEncoding)->querySelector('body');
        };
    }

    /** Return an assertable HTML element. */
    public function assertsElement(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): static {
            AssertableDocument::createFromString((string) $this, $options | LIBXML_NOERROR, $overrideEncoding)->with('body', $callback);

            return $this;
        };
    }
}
