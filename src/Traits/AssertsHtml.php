<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Traits;

use Ziadoz\AssertableHtml\Dom\AssertableDocument;

trait AssertsHtml
{
    /** Return a configured assertable HTML document. */
    public function assertableHtml(string $html, int $options = 0, ?string $overrideEncoding = null): AssertableDocument
    {
        return is_file($html)
            ? AssertableDocument::createFromFile($html, $options, $overrideEncoding)
            : AssertableDocument::createFromString($html, $options, $overrideEncoding);
    }

    /** Return an assertable HTML document. */
    public function assertHtml(string $html, callable $callback, int $options = 0, ?string $overrideEncoding = null): static
    {
        $this->assertableHtml($html, $options, $overrideEncoding)->with($callback);

        return $this;
    }

    /** Return an assertable HTML document scoped to <head>. */
    public function assertHead(string $html, callable $callback, int $options = 0, ?string $overrideEncoding = null): static
    {
        $this->assertableHtml($html, $options, $overrideEncoding)->one('head', $callback);

        return $this;
    }

    /** Return an assertable HTML document scoped to <body>. */
    public function assertBody(string $html, callable $callback, int $options = 0, ?string $overrideEncoding = null): static
    {
        $this->assertableHtml($html, $options, $overrideEncoding)->one('body', $callback);

        return $this;
    }

    /** Return an assertable HTML document scoped to the given selector. */
    public function assertElement(string $html, string $selector, callable $callback, int $options = 0, ?string $overrideEncoding = null): static
    {
        $this->assertableHtml($html, $options, $overrideEncoding)->one($selector, $callback);

        return $this;
    }
}
