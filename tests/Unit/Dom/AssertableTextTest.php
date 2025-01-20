<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class AssertableTextTest extends TestCase
{
    public function test_text(): void
    {
        $assertable = AssertableDocument::createFromString('<p>  <strong>Foo</strong>  Bar  </p>', LIBXML_NOERROR)
            ->querySelector('p')
            ->text;

        // Value
        $this->assertSame('  Foo  Bar  ', $assertable->value());
        $this->assertSame('Foo Bar', $assertable->value(true));

        // Starts With
        $this->assertTrue($assertable->startsWith('  Foo'));
        $this->assertTrue($assertable->startsWith('Foo', true));
        $this->assertFalse($assertable->startsWith('Qux'));
        $this->assertFalse($assertable->startsWith('Qux', true));

        // Ends With
        $this->assertTrue($assertable->endsWith('Bar  '));
        $this->assertTrue($assertable->endsWith('Bar', true));
        $this->assertFalse($assertable->endsWith('Qux'));
        $this->assertFalse($assertable->endsWith('Qux', true));

        // Contains
        $this->assertTrue($assertable->contains('o  B'));
        $this->assertTrue($assertable->contains('o B', true));
        $this->assertFalse($assertable->contains('Qux'));
        $this->assertFalse($assertable->contains('Qux', true));

        // Stringable
        $this->assertSame('  Foo  Bar  ', $assertable->__toString());
    }
}
