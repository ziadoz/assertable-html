<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Traits;

use Ziadoz\AssertableHtml\Dom\AssertableDocument;

trait AssertsHtml
{
    /** Return a configured assertable HTML document. */
    public function assertableDocument(string $html, int $options = 0, ?string $overrideEncoding = null): AssertableDocument
    {
        return is_file($html)
            ? AssertableDocument::createFromFile($html, $options, $overrideEncoding)
            : AssertableDocument::createFromString($html, $options, $overrideEncoding);
    }

    /** Return an assertable HTML document. */
    public function assertHtml(string $html, callable $callback, int $options = 0, ?string $overrideEncoding = null): void
    {
        $this->assertableDocument($html, $options, $overrideEncoding)->scope($callback);
    }

    /** Return an assertable HTML document scoped to <head>. */
    public function assertHead(string $html, callable $callback, int $options = 0, ?string $overrideEncoding = null): void
    {
        $this->assertableDocument($html, $options, $overrideEncoding)->with('head', $callback);
    }

    /** Return an assertable HTML document scoped to <body>. */
    public function assertBody(string $html, callable $callback, int $options = 0, ?string $overrideEncoding = null): void
    {
        $this->assertableDocument($html, $options, $overrideEncoding)->with('body', $callback);
    }
}
