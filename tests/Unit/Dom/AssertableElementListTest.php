<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

class AssertableElementListTest extends TestCase
{
    public function test_attribute_list(): void
    {
        $assertable = AssertableDocument::createFromString(<<<'HTML'
        <ul>
            <li id="foo">Foo</li>
            <li id="bar">Bar</li>
            <li id="baz">Baz</li>
        </ul>
        <p id="qux">Qux</p>
        HTML, LIBXML_HTML_NOIMPLIED)->querySelectorAll('li, p');

        // HTML
        $this->assertSame(<<<'HTML'
        <li id="foo">Foo</li>
        <li id="bar">Bar</li>
        <li id="baz">Baz</li>
        <p id="qux">Qux</p>
        HTML, $assertable->getHtml());

        // Empty
        $this->assertFalse($assertable->empty());

        // Nth / First / Last
        $this->assertSame('foo', $assertable->first()->id);
        $this->assertSame('bar', $assertable->nth(1)->id);
        $this->assertSame('baz', $assertable->nth(2)->id);
        $this->assertSame('qux', $assertable->last()->id);

        // Each
        $ids = ['foo', 'bar', 'baz', 'qux'];
        $assertable->each(function (AssertableElement $el, int $index) use ($ids): void {
            $this->assertSame($ids[$index], $el->id);
        });

        // Array Access
        $this->assertTrue(isset($assertable[0]));
        $this->assertSame('foo', $assertable[0]->id);
        $this->assertTrue(isset($assertable[1]));
        $this->assertSame('bar', $assertable[1]->id);
        $this->assertTrue(isset($assertable[2]));
        $this->assertSame('baz', $assertable[2]->id);
        $this->assertTrue(isset($assertable[3]));
        $this->assertSame('qux', $assertable[3]->id);
        $this->assertNull($assertable[4] ?? null);

        // Count
        $this->assertCount(4, $assertable);
    }
}
