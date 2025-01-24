<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Mixins;

use Closure;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class TestViewMixins
{
    /** Return an assertable HTML element. */
    public function assertsElement(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): void {
            AssertableDocument::createFromString((string) $this, $options | LIBXML_NOERROR, $overrideEncoding)->with('body', $callback);
        };
    }
}
