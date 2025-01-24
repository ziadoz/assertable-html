<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableAttributesList;
use Ziadoz\AssertableHtml\Dom\AssertableClassList;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElementsList;
use Ziadoz\AssertableHtml\Dom\AssertableText;

class AssertableElementTest extends TestCase
{
    public function test_properties(): void
    {
        $assertable = AssertableDocument::createFromString(
            '<p id="foo" class="foo bar" data-baz="qux"><strong>Foo</strong></p>',
            LIBXML_NOERROR,
        )->querySelector('p');

        $this->assertSame('<strong>Foo</strong>', $assertable->html);
        $this->assertInstanceOf(AssertableClassList::class, $assertable->classes);
        $this->assertSame(['foo', 'bar'], $assertable->classes->toArray());
        $this->assertInstanceOf(AssertableAttributesList::class, $assertable->attributes);
        $this->assertSame(['id' => 'foo', 'class' => 'foo bar', 'data-baz' => 'qux'], $assertable->attributes->toArray());
        $this->assertSame('p', $assertable->tag);
        $this->assertSame('foo', $assertable->id);
        $this->assertInstanceOf(AssertableText::class, $assertable->text);
        $this->assertSame('Foo', $assertable->text->__toString());
    }

    public function test_get_html(): void
    {
        $assertable = AssertableDocument::createFromString('<p>Foo</p>', LIBXML_NOERROR);

        $this->assertSame('<html><head></head><body><p>Foo</p></body></html>', $assertable->getHtml());
    }

    /*
    |--------------------------------------------------------------------------
    | Native
    |--------------------------------------------------------------------------
    */

    public function test_contains(): void
    {
        $assertable = AssertableDocument::createFromString('<div><ul><li>Foo</li></ul><p>Bar</p></div>', LIBXML_NOERROR)
            ->querySelector('div');

        $this->assertTrue($assertable->querySelector('ul')->contains($assertable->querySelector('li')));
        $this->assertFalse($assertable->querySelector('ul')->contains($assertable->querySelector('p')));
    }

    public function test_closest(): void
    {
        $assertable = AssertableDocument::createFromString('<div><ul><li>Foo</li></ul></div>', LIBXML_NOERROR)
            ->querySelector('div');

        $this->assertInstanceOf(AssertableElement::class, $assertable->querySelector('li')->closest('ul'));
        $this->assertNull($assertable->querySelector('li')->closest('foo'));
    }

    public function test_query_selector(): void
    {
        $assertable = AssertableDocument::createFromString('<div><ul><li>Foo</li></ul></div>', LIBXML_NOERROR)
            ->querySelector('div');

        $this->assertInstanceOf(AssertableElement::class, $assertable->querySelector('li'));
    }

    public function test_query_selector_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [div] doesn't contain an element matching the given selectors [foo].");

        AssertableDocument::createFromString('<div><ul><li>Foo</li></ul></div>', LIBXML_NOERROR)
            ->querySelector('div')
            ->querySelector('foo');
    }

    public function test_query_selector_all(): void
    {
        $assertable = AssertableDocument::createFromString('<ul><li>Foo</li>><li>Bar</li></ul>', LIBXML_NOERROR)
            ->querySelector('ul');

        $list = $assertable->querySelectorAll('li');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(2, $list);

        $list = $assertable->querySelectorAll('foo');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(0, $list);
    }

    public function test_get_element_by_tag_name(): void
    {
        $assertable = AssertableDocument::createFromString('<ul><li>Foo</li><li>Bar</li></ul>', LIBXML_NOERROR)
            ->querySelector('ul');

        $list = $assertable->getElementsByTagName('li');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(2, $list);

        $list = $assertable->querySelectorAll('foo');
        $this->assertInstanceOf(AssertableElementsList::class, $list);
        $this->assertCount(0, $list);
    }
}
