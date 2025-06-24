<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Concerns\Asserts;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class AssertsDocumentTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Assert Title
    |--------------------------------------------------------------------------
    */

    public function test_assert_title_equals_passes(): void
    {
        AssertableDocument::createFromString('<title>Foo - Bar</title>', LIBXML_HTML_NOIMPLIED)
            ->assertTitleEquals('Foo - Bar');
    }

    public function test_assert_title_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The page title doesn't equal the given title.");

        AssertableDocument::createFromString('<title>Foo - Bar</title>', LIBXML_HTML_NOIMPLIED)
            ->assertTitleEquals('Baz - Qux');
    }

    public function test_assert_title_doesnt_equal_passes(): void
    {
        AssertableDocument::createFromString('<title>Foo - Bar</title>', LIBXML_HTML_NOIMPLIED)
            ->assertTitleDoesntEqual('Baz - Qux');
    }

    public function test_assert_title_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The page title equals the given title.');

        AssertableDocument::createFromString('<title>Foo - Bar</title>', LIBXML_HTML_NOIMPLIED)
            ->assertTitleDoesntEqual('Foo - Bar');
    }
}
