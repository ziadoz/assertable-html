<?php
namespace Ziadoz\AssertableHtml\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Ziadoz\AssertableHtml\Utilities;

class UtilitiesTest extends TestCase
{
    #[DataProvider('selectorFromElementDataProvider')]
    public function testSelectorFromElement(string $expected, string $html, string $selector): void
    {
        $this->assertSame(
            $expected,
            Utilities::selectorFromElement($this->getFixtureElement($html, $selector)),
        );
    }

    public static function selectorFromElementDataProvider(): iterable
    {
        yield 'element' => [
            'p',
            '<p>Foo</p>',
            'p',
        ];

        yield 'element with id' => [
            'p#qux',
            '<p id="qux">Foo</p>',
            'p',
        ];

        yield 'element with class' => [
            'p.foo.bar.baz',
            '<p class="foo bar baz">Foo</p>',
            'p',
        ];

        yield 'element with id and class' => [
            'p#qux.foo.bar.baz',
            '<p class="foo bar baz" id="qux">Foo</p>',
            'p',
        ];

        yield 'element with whitespaced classes' => [
            'p.foo.bar.baz',
            '<p class="' . "\t" . '  foo  ' . "\n" . '  bar  ' . "\n" . '  baz  ' . "\t" . '">Foo</p>',
            'p',
        ];
    }

    public function testNormaliseWhitespace(): void
    {
        $this->assertSame(
            'foo bar baz',
            Utilities::normaliseWhitespace("\t\t" . '  foo  ' . "\n\r" . '  bar  ' . "\r\t" . '  baz  ' . "\n\n"),
        );
    }
}
