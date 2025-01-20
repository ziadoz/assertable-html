<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use Closure;
use Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElementsList;

class AssertableDocumentTest extends TestCase
{
    public function test_properties(): void
    {
        $assertable = AssertableDocument::createFromString('<title>Foo - Bar</title>', LIBXML_NOERROR);
        $this->assertSame('Foo - Bar', $assertable->title);
        $this->assertInstanceOf(AssertableElement::class, $assertable->head);
        $this->assertInstanceOf(AssertableElement::class, $assertable->body);
    }

    public function test_get_html(): void
    {
        $assertable = AssertableDocument::createFromString('<p>Foo</p>', LIBXML_NOERROR);
        $this->assertSame('<html><head></head><body><p>Foo</p></body></html>', $assertable->getHtml());
    }

    public function test_proxy(): void
    {
        $proxy = AssertableDocument::proxy(HTMLDocument::createFromString('<p>Foo</p>', LIBXML_NOERROR));
        $this->assertInstanceOf(AssertableDocument::class, $proxy);
        $this->assertInstanceOf(Closure::class, new ReflectionClass($proxy)->getLazyInitializer($proxy));
    }

    public function test_create_from_string(): void
    {
        $assertable = AssertableDocument::createFromString('<p>Foo</p>', LIBXML_NOERROR);
        $this->assertInstanceOf(AssertableDocument::class, $assertable);
    }

    public function test_create_from_file(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'assertable-html');
        file_put_contents($file, '<p>Foo.</p>');

        $assertable = AssertableDocument::createFromFile($file, LIBXML_NOERROR);
        $this->assertInstanceOf(AssertableDocument::class, $assertable);

        @unlink($file);
    }

    public function test_query_selector(): void
    {
        $assertable = AssertableDocument::createFromString('<p>Foo</p>', LIBXML_NOERROR);
        $this->assertInstanceOf(AssertableElement::class, $assertable->querySelector('p'));
        $this->assertNull($assertable->querySelector('foo'));
    }

    public function test_query_selector_all(): void
    {
        $assertable = AssertableDocument::createFromString('<ul><li>Foo</li><li>Bar</li></ul>', LIBXML_NOERROR);

        $list = $assertable->querySelectorAll('li');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(2, $list);

        $list = $assertable->querySelectorAll('foo');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(0, $list);
    }

    public function test_get_element_by_id(): void
    {
        $assertable = AssertableDocument::createFromString('<p id="foo">Foo</p>', LIBXML_NOERROR);

        $this->assertInstanceOf(AssertableElement::class, $assertable->getElementById('foo'));
        $this->assertNull($assertable->getElementById('bar'));
    }

    public function test_get_element_by_tag_name(): void
    {
        $assertable = AssertableDocument::createFromString('<ul><li>Foo</li><li>Bar</li></ul>', LIBXML_NOERROR);

        $list = $assertable->getElementsByTagName('li');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(2, $list);

        $list = $assertable->querySelectorAll('foo');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(0, $list);
    }
}
