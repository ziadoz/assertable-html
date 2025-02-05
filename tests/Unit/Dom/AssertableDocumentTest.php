<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElementsList;
use Ziadoz\AssertableHtml\Exceptions\UnableToCreateAssertableDocument;

class AssertableDocumentTest extends TestCase
{
    public function test_properties(): void
    {
        $assertable = AssertableDocument::createFromString('<title>Foo - Bar</title>', LIBXML_HTML_NOIMPLIED);
        $this->assertSame('Foo - Bar', $assertable->title);
    }

    public function test_get_html(): void
    {
        $assertable = AssertableDocument::createFromString('<p>Foo</p>', LIBXML_HTML_NOIMPLIED);
        $this->assertSame('<p>Foo</p>', $assertable->getHtml());
    }

    /*
    |--------------------------------------------------------------------------
    | Native
    |--------------------------------------------------------------------------
    */

    public function test_create_from_string(): void
    {
        $assertable = AssertableDocument::createFromString('<p>Foo</p>', LIBXML_HTML_NOIMPLIED);
        $this->assertInstanceOf(AssertableDocument::class, $assertable);
    }

    public function test_create_from_string_throws_exception(): void
    {
        $this->expectException(UnableToCreateAssertableDocument::class);
        $this->expectExceptionMessage('Unable to create assertable HTML document.');

        AssertableDocument::createFromString('~~~><~~~I am invalid HTML~~~><~~~');
    }

    public function test_create_from_file(): void
    {
        try {
            $file = tempnam(sys_get_temp_dir(), 'assertable-html');
            file_put_contents($file, '<p>Foo.</p>');

            $assertable = AssertableDocument::createFromFile($file, LIBXML_HTML_NOIMPLIED);
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
        } finally {
            @unlink($file);
        }
    }

    public function test_create_from_file_throws_exception(): void
    {
        $this->expectException(UnableToCreateAssertableDocument::class);
        $this->expectExceptionMessage('Unable to create assertable HTML document.');

        try {
            $file = tempnam(sys_get_temp_dir(), 'assertable-html');
            file_put_contents($file, '~~~><~~~I am invalid HTML~~~><~~~');

            AssertableDocument::createFromFile($file);
        } finally {
            @unlink($file);
        }
    }

    public function test_query_selector(): void
    {
        $assertable = AssertableDocument::createFromString('<p>Foo</p>', LIBXML_HTML_NOIMPLIED);
        $this->assertInstanceOf(AssertableElement::class, $assertable->querySelector('p'));
    }

    public function test_query_selector_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The document doesn't contain an element matching the given selector [foo].");

        AssertableDocument::createFromString('<p>Foo</p>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('foo');
    }

    public function test_query_selector_all(): void
    {
        $assertable = AssertableDocument::createFromString('<ul><li>Foo</li><li>Bar</li></ul>', LIBXML_HTML_NOIMPLIED);

        $list = $assertable->querySelectorAll('li');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(2, $list);

        $list = $assertable->querySelectorAll('foo');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(0, $list);
    }

    public function test_get_element_by_id(): void
    {
        $assertable = AssertableDocument::createFromString('<p id="foo">Foo</p>', LIBXML_HTML_NOIMPLIED);

        $this->assertInstanceOf(AssertableElement::class, $assertable->getElementById('foo'));
    }

    public function test_get_element_by_id_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The document doesn't contain an element matching the given ID [bar].");

        $assertable = AssertableDocument::createFromString('<p id="foo">Foo</p>', LIBXML_HTML_NOIMPLIED)
            ->getElementById('bar');
    }

    public function test_get_element_by_tag_name(): void
    {
        $assertable = AssertableDocument::createFromString('<ul><li>Foo</li><li>Bar</li></ul>', LIBXML_HTML_NOIMPLIED);

        $list = $assertable->getElementsByTagName('li');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(2, $list);

        $list = $assertable->querySelectorAll('foo');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(0, $list);
    }
}
