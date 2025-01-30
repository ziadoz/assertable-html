<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use OutOfBoundsException;
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
            $this->assertStringContainsString($attrs[$index][1], $value);
        });

        // Sequence
        $assertable->sequence(...array_fill(0, 4, function (string $attribute, ?string $value, int $sequence) use ($attrs) {
            $this->assertSame($attrs[$sequence][0], $attribute);
            $this->assertStringContainsString($attrs[$sequence][1], $value);
        }));

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

    public function test_sequence_throws(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Missing sequence callback for attribute [class] at position [1].');

        AssertableDocument::createFromString('<p id="foo" class="bar">Foo</p>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('p')
            ->attributes
            ->sequence(
                fn (string $attribute, ?string $value, int $sequence) => $this->assertSame('foo', $value),
            );
    }
}
