<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Mixins;

use Closure;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class TestResponseMixins
{
    /** Return an assertable HTML document. */
    public function assertsHtml(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): void {
            AssertableDocument::createFromString($this->getContent(), $options, $overrideEncoding)->scope($callback);
        };
    }

    /** Return an assertable HTML document scoped to <head>. */
    public function assertsHead(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): void {
            AssertableDocument::createFromString($this->getContent(), $options, $overrideEncoding)->with('head', $callback);
        };
    }

    /** Return an assertable HTML document scoped to <body>. */
    public function assertsBody(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): void {
            AssertableDocument::createFromString($this->getContent(), $options, $overrideEncoding)->with('body', $callback);
        };
    }
}
