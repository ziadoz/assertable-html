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

        // Stringable
        $this->assertSame('  Foo  Bar  ', $assertable->__toString());
    }
}
