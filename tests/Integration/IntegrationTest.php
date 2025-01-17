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

                <!-- Span -->
                <span data-foo="foo-bar" aria-label="foo-bar">I am a test span.</span>

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

                <-- Custom Element -->
                <my-web-component>I am a web component.</my-web-component>
            </body>
            </html>
        HTML, LIBXML_NOERROR);

        // With / Elsewhere
        $html->with('ul', function (AssertableHtmlElement $el): void {
            $el->assertElementsCount('li', 3);
            $el->with('li:nth-child(1)', fn (AssertableHtmlElement $el) => $el->assertAttributeEquals('id', 'foo'));
            $el->with('li:nth-child(2)', fn (AssertableHtmlElement $el) => $el->assertAttributeEquals('id', 'bar'));
            $el->with('li:nth-child(3)', fn (AssertableHtmlElement $el) => $el->assertAttributeEquals('id', 'baz'));

            $el->elsewhere('div', function (AssertableHtmlElement $el): void {
                $el->assertTextEquals('This is a test div.', true);
            });
        });

        // When
        $html->when(true, function (AssertableHtmlDocument $doc): void {
            $doc->querySelectorAll('ul li')->assertCount(3);
        });

        $html->when(false, null, function (AssertableHtmlDocument $doc): void {
            $doc->querySelectorAll('ul li')->assertCount(3);
        });

        $html->querySelector('ul')->when(true, function (AssertableHtmlElement $el): void {
            $el->assertTextEquals('Foo Bar Baz', true);
        });

        $html->querySelector('ul')->when(true, null, function (AssertableHtmlElement $el): void {
            $el->assertTextEquals('Foo Bar Baz', true);
        });

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
            ->assertTextEquals('This is a test div.', true)
            ->assertTextDoesntEqual('This is NOT a test div.', true)
            ->assertTextStartsWith('This is', true)
            ->assertTextDoesntStartWith('This is NOT', true)
            ->assertTextEndsWith('a test div.', true)
            ->assertTextDoesntEndWith('a test span', true)
            ->assertText(fn (AssertableText $text) => $text->contains('is a test', true));

        $html->querySelector('p')
            ->assertClassesNotEmpty()
            ->assertClassPresent()
            ->assertClassEquals('lux pux nux')
            ->assertClassDoesntEqual('foo bar baz')
            ->assertClassContains('pux')
            ->assertClassDoesntContain('bar')
            ->assertClassContainsAll(['lux'])
            ->assertClassContainsAll(['pux', 'lux', 'nux'])
            ->assertClassDoesntContainAll(['pux', 'lux', 'nux', 'foo', 'bar', 'baz'])
            ->assertClassContainsAny(['foo', 'bar', 'lux'])
            ->assertClassDoesntContainAny(['foo', 'bar', 'baz'])
            ->assertClass(function (AssertableClassList $classes): bool {
                return $classes->contains('lux') &&
                    $classes->contains('pux') &&
                    $classes->contains('nux');
            });

        $html->querySelector('div')
            ->assertIdEquals('foo-bar')
            ->assertIdDoesntEqual('baz-qux')
            ->assertAttributesNotEmpty()
            ->assertAttributePresent('id')
            ->assertAttributeMissing('foo-bar')
            ->assertAttributeEquals('id', 'foo-bar')
            ->assertAttributeDoesntEqual('id', 'baz-qux')
            ->assertAttributeStartsWith('id', 'foo-')
            ->assertAttributeDoesntStartWith('id', 'bar-')
            ->assertAttributeEndsWith('id', '-bar')
            ->assertAttributeDoesntEndWith('id', '-foo')
            ->assertAttributeContains('id', 'o-b')
            ->assertAttributeDoesntContain('id', 'qux')
            ->assertAttributesEqualArray([
                'data-bar' => 'baz-buz',
                'data-qux' => 'lux-pux',
                'id'       => 'foo-bar',
            ])
            ->assertAttributes(function (AssertableAttributesList $attributes): bool {
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
            })
            ->assertAttribute('id', function (?string $value): bool {
                return $value === 'foo-bar';
            });

        $html->querySelector('span')
            ->assertDataAttributePresent('foo')
            ->assertDataAttributeEquals('foo', 'foo-bar')
            ->assertDataAttributeDoesntEqual('foo', 'baz-qux')
            ->assertDataAttributeContains('foo', 'foo')
            ->assertDataAttributeDoesntContain('foo', 'baz')
            ->assertDataAttribute('foo', function (?string $value): bool {
                return $value === 'foo-bar';
            })
            ->assertAriaAttributePresent('label')
            ->assertAriaAttributeEquals('label', 'foo-bar')
            ->assertAriaAttributeDoesntEqual('label', 'baz-qux')
            ->assertAriaAttributeContains('label', 'foo')
            ->assertAriaAttributeDoesntContain('label', 'baz')
            ->assertAriaAttribute('label', function (?string $value): bool {
                return $value === 'foo-bar';
            });

        $html->querySelector('my-web-component')
            ->assertTag('my-web-component');

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
            ->assertEqualsArray([
                'data-bar' => 'baz-buz',
                'data-qux' => 'lux-pux',
                'id'       => 'foo-bar',
            ])
            ->assertAttributes(function (AssertableAttributesList $attributes): bool {
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
            })
            ->assertAttribute('id', function (?string $value): bool {
                return $value === 'foo-bar';
            });

        // Assertable Class List
        $html->querySelector('p')
            ->classes
            ->assertNotEmpty()
            ->assertContains('lux')
            ->assertDoesntContain('tux')
            ->assertContainsAll(['pux', 'lux', 'nux'])
            ->assertDoesntContainAll(['pux', 'lux', 'nux', 'foo', 'bar', 'baz'])
            ->assertContainsAny(['foo', 'bar', 'lux'])
            ->assertDoesntContainAny(['foo', 'bar', 'baz'])
            ->assertValueEquals('lux pux nux')
            ->assertValueDoesntEqual('foo bar baz')
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
            ->assertText(function (AssertableText $text): bool {
                return $text->startsWith('I') && $text->endsWith('.') && $text->contains('test');
            });
    }
}
