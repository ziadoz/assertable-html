<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Ziadoz\AssertableHtml\Prototype\Dom\AssertableAttributesList;
use Ziadoz\AssertableHtml\Prototype\Dom\AssertableClassList;
use Ziadoz\AssertableHtml\Prototype\Dom\AssertableHtmlDocument;
use Ziadoz\AssertableHtml\Prototype\Dom\AssertableHtmlElement;
use Ziadoz\AssertableHtml\Prototype\Dom\AssertableHtmlElementsList;
use Ziadoz\AssertableHtml\Prototype\Dom\AssertableText;
use Ziadoz\AssertableHtml\Tests\TestCase;

class IntegrationTest extends TestCase
{
    public function test_assertable_html(): void
    {
        $html = AssertableHtmlDocument::createFromString(<<<'HTML'
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Page Title</title>
            </head>
            <body>
                <!-- Paragraph -->
                <p class="lux pux nux" id="qux">I am a test paragraph.</p>

                <!-- Div -->
                <div id="foo-bar" data-bar="baz-buz" data-qux="lux-pux">
                    This is a test div.
                </div>

                <!-- Unordered List -->
                <ul id="list">
                    <li id="foo">Foo</li>
                    <li id="bar">Bar</li>
                    <li id="baz">Baz</li>
                </ul>

                <!-- Form -->
                <form method="get" action="/foo/bar" enctype="multipart/form-data">
                    <label>Name <input type="text" name="name" value="Foo Bar"></label>
                    <label>Age <input type="number" name="age" value="42"></label>
                    <button type="submit">Save</button>
                </form>
            </body>
            </html>
        HTML);

        // Assertable Element List
        $html->querySelectorAll('ul li')
            ->assertCount(3)
            ->assertLessThan(4)
            ->assertLessThanOrEqual(4)
            ->assertGreaterThan(1)
            ->assertGreaterThanOrEqual(1)
            ->assertAny(fn (AssertableHtmlElement $el) => $el->matches('li'))
            ->assertAll(fn (AssertableHtmlElement $el) => $el->matches('li[id]'))
            ->assertElements(function (AssertableHtmlElementsList $els): bool {
                return $els[0]->matches('li[id="foo"]') && $els[1]->matches('li[id="bar"]') && $els[2]->matches('li[id="baz"]');
            });

        // Assertable Attributes List
        $html->querySelector('div')
            ->attributes
            ->assertNotEmpty()
            ->assertPresent('id')
            ->assertMissing('foo-bar')
            ->assertEquals('id', 'foo-bar')
            ->assertDoesntEqual('id', 'baz-qux')
            ->assertStartsWith('id', 'foo-')
            ->assertDoesntStartWith('id', 'bar-')
            ->assertEndsWith('id', '-bar')
            ->assertDoesntEndWith('id', '-foo')
            ->assertContains('id', 'o-b')
            ->assertDoesntContain('id', 'qux')
            ->assertAttributes(function (AssertableAttributesList $attributes) {
                return (
                    $attributes['id'] === 'foo-bar' &&
                    $attributes['data-bar'] === 'baz-buz' &&
                    $attributes['data-qux'] === 'lux-pux'
                ) && (
                    $attributes->startsWith('id', 'foo-') &&
                    $attributes->endsWith('data-bar', '-buz') &&
                    $attributes->contains('data-qux', 'x-p')
                ) && (
                    $attributes->present('id') &&
                    $attributes->missing('foo-bar')
                );
            });

        // Assertable Class List
        $html->querySelector('p')
            ->classes
            ->assertNotEmpty()
            ->assertContains('lux')
            ->assertDoesntContain('tux')
            ->assertContainsAll(['pux', 'lux', 'nux'])
            ->assertContainsAny(['tux', 'wux', 'lux'])
            ->assertValueEquals('lux pux nux')
            ->assertValueDoesntEqual('tux wux lux')
            ->assertClasses(function (AssertableClassList $classes): bool {
                return $classes->contains('lux');
            });

        // Assertable Text
        $html->querySelector('p')
            ->text
            ->assertSame('I am a test paragraph.')
            ->assertNotSame('foo bar')
            ->assertSeeIn('paragraph')
            ->assertDontSeeIn('foo bar')
            ->assertStartsWith('I am')
            ->assertDoesntStartWith('foo bar')
            ->assertEndsWith('paragraph.')
            ->assertDoesntEndWith('foo bar')
            ->assertText(function (AssertableText $text) {
                return $text->startsWith('I') && $text->endsWith('.') && $text->contains('test');
            });
    }
}
