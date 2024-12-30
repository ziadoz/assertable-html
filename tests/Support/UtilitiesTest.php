<?php

namespace Ziadoz\AssertableHtml\Tests\Support;

use Dom\HTMLDocument;
use PHPUnit\Framework\Attributes\DataProvider;
use Ziadoz\AssertableHtml\Support\Utilities;
use Ziadoz\AssertableHtml\Tests\TestCase;

class UtilitiesTest extends TestCase
{
    #[DataProvider('selectorFromElementDataProvider')]
    public function test_selector_from_element(string $expected, string $html, string $selector): void
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

    public function test_normalise_whitespace(): void
    {
        $this->assertSame(
            'foo bar baz',
            Utilities::normaliseWhitespace("\t\t" . '  foo  ' . "\n\r" . '  bar  ' . "\r\t" . '  baz  ' . "\n\n"),
        );
    }

    public function test_nodes_to_matches_html(): void
    {
        $nodes = HTMLDocument::createFromString(
            <<<'HTML'
            <p class="foo" id="foo">
                <span class="foo">Foo</span>
            </p>
            <p class="bar">
                <strong>Bar</strong>
            </p>
            <p id="baz">Baz</p>
            <p class="qux">Qux</p>
            HTML,
            LIBXML_NOERROR,
        )->querySelectorAll('p');

        $this->assertSame(
            <<<'OUTPUT'
            4 Matching Element(s) Found
            ============================

            1. [p#foo.foo]:
            > <p class="foo" id="foo">
            >     <span class="foo">Foo</span>
            > </p>

            2. [p.bar]:
            > <p class="bar">
            >     <strong>Bar</strong>
            > </p>

            2 Matching Elements Omitted...
            OUTPUT,
            Utilities::nodesToMatchesHtml($nodes, 2),
        );
    }
}
