<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Mixins;

use Closure;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class TestResponseMixins
{
    /** Get the response HTML content. */
    public function getHtmlContent(): Closure
    {
        return fn (): string => $this->baseResponse instanceof StreamedResponse ? $this->streamedContent() : $this->getContent();
    }

    /** Return an assertable HTML document. */
    public function assertableHtml(): Closure
    {
        return function (int $options = 0, ?string $overrideEncoding = null): AssertableDocument {
            return AssertableDocument::createFromString($this->getHtmlContent(), $options, $overrideEncoding);
        };
    }

    /** Return an assertable HTML document. */
    public function assertHtml(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): static {
            AssertableDocument::createFromString($this->getHtmlContent(), $options, $overrideEncoding)->scope($callback);

            return $this;
        };
    }

    /** Return an assertable HTML document scoped to <head>. */
    public function assertHead(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): static {
            AssertableDocument::createFromString($this->getHtmlContent(), $options, $overrideEncoding)->with('head', $callback);

            return $this;
        };
    }

    /** Return an assertable HTML document scoped to <body>. */
    public function assertBody(): Closure
    {
        return function (callable $callback, int $options = 0, ?string $overrideEncoding = null): static {
            AssertableDocument::createFromString($this->getHtmlContent(), $options, $overrideEncoding)->with('body', $callback);

            return $this;
        };
    }

    /** Return an assertable HTML document scoped to the given selector. */
    public function assertElement(): Closure
    {
        return function (string $selector, callable $callback, int $options = 0, ?string $overrideEncoding = null): static {
            AssertableDocument::createFromString($this->getHtmlContent(), $options, $overrideEncoding)->with($selector, $callback);

            return $this;
        };
    }
}
