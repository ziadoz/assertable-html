<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableAttributesList;
use Ziadoz\AssertableHtml\Dom\AssertableClassList;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
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
}
