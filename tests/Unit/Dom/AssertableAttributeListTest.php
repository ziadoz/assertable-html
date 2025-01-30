<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class AssertableAttributeListTest extends TestCase
{
    public function test_attribute_list(): void
    {
        $assertable = AssertableDocument::createFromString('<p class="foo" id="bar" data-bar="  foo-bar  " aria-label="foo">Foo</p>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('p')
            ->attributes;

        // Value
        $this->assertSame('foo', $assertable->value('class'));
        $this->assertSame('bar', $assertable->value('id'));
        $this->assertSame('  foo-bar  ', $assertable->value('data-bar'));
        $this->assertSame('foo-bar', $assertable->value('data-bar', true));
        $this->assertSame('foo', $assertable->value('aria-label'));

        // Empty
        $this->assertFalse($assertable->empty());

        // Names
        $this->assertSame(['class', 'id', 'data-bar', 'aria-label'], $assertable->names());

        // Has
        $this->assertTrue($assertable->has('class'));
        $this->assertTrue($assertable->has('id'));
        $this->assertTrue($assertable->has('data-bar'));
        $this->assertTrue($assertable->has('aria-label'));
        $this->assertFalse($assertable->has('foo-bar'));

        // Each
        $attrs = [['class', 'foo'], ['id', 'bar'], ['data-bar', 'foo-bar'], ['aria-label', 'foo']];
        $assertable->each(function (string $attribute, ?string $value, int $index) use ($attrs): void {
            $this->assertSame($attrs[$index][0], $attribute);
            $this->assertSame($attrs[$index][1], $value);
        }, true);

        // Array Access
        $this->assertTrue(isset($assertable['class']));
        $this->assertSame('foo', $assertable['class']);
        $this->assertTrue(isset($assertable['id']));
        $this->assertSame('bar', $assertable['id']);
        $this->assertTrue(isset($assertable['data-bar']));
        $this->assertSame('  foo-bar  ', $assertable['data-bar']);
        $this->assertTrue(isset($assertable['aria-label']));
        $this->assertSame('foo', $assertable['aria-label']);
        $this->assertNull($assertable['foo-bar-baz'] ?? null);

        // Count
        $this->assertCount(4, $assertable);

        // Stringable
        $this->assertSame('class="foo" id="bar" data-bar="  foo-bar  " aria-label="foo"', $assertable->__toString());

        // Array
        $this->assertSame([
            'class'      => 'foo',
            'id'         => 'bar',
            'data-bar'   => '  foo-bar  ',
            'aria-label' => 'foo',
        ], $assertable->toArray());
    }
}
