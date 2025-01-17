<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Ziadoz\AssertableHtml\Dom\AssertableAttributesList;
use Ziadoz\AssertableHtml\Dom\AssertableClassList;
use Ziadoz\AssertableHtml\Dom\AssertableHtmlDocument;
use Ziadoz\AssertableHtml\Dom\AssertableHtmlElement;
use Ziadoz\AssertableHtml\Dom\AssertableHtmlElementsList;
use Ziadoz\AssertableHtml\Dom\AssertableText;
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

        // Assertable HTML Element
        $html->querySelector('div')
            ->assertTitleEquals('Test Page Title');

        $html->querySelector('div')
            ->assertElement(function (AssertableHtmlElement $el): bool {
                return
                    $el->id === 'foo-bar' &&
                    $el->attributes->contains('data-bar', 'baz') &&
                    $el->attributes->value('data-qux') === 'lux-pux';
            });

        $html->querySelector('div')
            ->assertMatchesSelector('div#foo-bar')
            ->assertDoesntMatchSelector('span[data-foo-bar]');

        $html->querySelector('ul')
            ->assertNumberOfElements('li', '=', 3)
            ->assertElementsCount('li', 3)
            ->assertElementsCountGreaterThan('li', 1)
            ->assertElementsCountGreaterThanOrEqual('li', 3)
            ->assertElementsCountLessThan('li', 4)
            ->assertElementsCountLessThanOrEqual('li', 3);

        $html->querySelector('div')
            ->assertText(fn (AssertableText $text) => $text->contains('is a test', true))
            ->assertTextEquals('This is a test div.', true)
            ->assertTextDoesntEqual('This is NOT a test div.', true)
            ->assertTextStartsWith('This is', true)
            ->assertTextDoesntStartWith('This is NOT', true)
            ->assertTextEndsWith('a test div.', true)
            ->assertTextDoesntEndWith('a test span', true);

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
            ->assertEquals('I am a test paragraph.')
            ->assertDoesntEqual('foo bar')
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
