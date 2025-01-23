<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Mixins;

use Closure;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class TestResponseMixins
{
    /** Return an assertable HTML document. */
    public function assertHtml(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): void {
            AssertableDocument::createFromString(
                $this->baseResponse instanceof StreamedResponse ? $this->streamedContent() : $this->getContent(),
                $options,
                $overrideEncoding
            )->scope($callback);
        };
    }

    /** Return an assertable HTML document scoped to <head>. */
    public function assertHead(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): void {
            AssertableDocument::createFromString(
                $this->baseResponse instanceof StreamedResponse ? $this->streamedContent() : $this->getContent(),
                $options,
                $overrideEncoding
            )->with('head', $callback);
        };
    }

    /** Return an assertable HTML document scoped to <body>. */
    public function assertBody(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): void {
            AssertableDocument::createFromString(
                $this->baseResponse instanceof StreamedResponse ? $this->streamedContent() : $this->getContent(),
                $options,
                $overrideEncoding
            )->with('body', $callback);
        };
    }
}
