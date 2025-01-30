<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class AssertableClassesListTest extends TestCase
{
    public function test_class_list(): void
    {
        $assertable = AssertableDocument::createFromString('<p class="  foo  bar  baz  ">Foo</p>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('p')
            ->classes;

        // Value
        $this->assertSame('  foo  bar  baz  ', $assertable->value());
        $this->assertSame('foo bar baz', $assertable->value(true));

        // Empty
        $this->assertFalse($assertable->empty());

        // Contains
        $this->assertTrue($assertable->contains('foo'));
        $this->assertFalse($assertable->contains('qux'));

        // Each
        $classes = ['foo', 'bar', 'baz'];
        $assertable->each(function (string $class, int $index) use ($classes): void {
            $this->assertSame($classes[$index], $class);
        });

        // Sequence
        $assertable->sequence(...array_fill(0, 3, function (string $class, int $index) use ($classes) {
            $this->assertSame($classes[$index], $class);
        }));

        // Any
        $this->assertTrue($assertable->any(['foo', 'qux']));
        $this->assertFalse($assertable->any(['qux', 'lux']));

        // All
        $this->assertTrue($assertable->all(['foo', 'bar', 'baz']));
        $this->assertFalse($assertable->all(['foo', 'bar', 'baz', 'qux']));

        // Array Access
        $this->assertTrue(isset($assertable[0]));
        $this->assertSame('foo', $assertable[0]);
        $this->assertTrue(isset($assertable[1]));
        $this->assertSame('bar', $assertable[1]);
        $this->assertTrue(isset($assertable[2]));
        $this->assertSame('baz', $assertable[2]);
        $this->assertFalse(isset($assertable[3]));
        $this->assertNull($assertable[3] ?? null);

        // Count
        $this->assertCount(3, $assertable);

        // Stringable
        $this->assertSame('foo bar baz', $assertable->__toString());

        // Array
        $this->assertSame(['foo', 'bar', 'baz'], $assertable->toArray());
    }

    public function test_sequence_throws(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Missing sequence callback for class at position [1].');

        AssertableDocument::createFromString('<p class="foo bar baz">Foo</p>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('p')
            ->classes
            ->sequence(
                fn (string $class, int $sequence) => $this->assertSame('foo', $class),
            );
    }
}
