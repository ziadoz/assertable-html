<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Support;

use Ziadoz\AssertableHtml\Support\Whitespace;
use Ziadoz\AssertableHtml\Tests\TestCase;

class WhitespaceTest extends TestCase
{
    public function test_normalise(): void
    {
        $this->assertSame(
            'foo bar baz',
            Whitespace::normalise("\t\t" . '  foo  ' . "\n\r" . '  bar  ' . "\r\t" . '  baz  ' . "\n\n"),
        );
    }
}
