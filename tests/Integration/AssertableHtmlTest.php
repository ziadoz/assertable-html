<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableAttributesList;
use Ziadoz\AssertableHtml\Dom\AssertableClassesList;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElementsList;
use Ziadoz\AssertableHtml\Dom\AssertableText;

class AssertableHtmlTest extends TestCase
{
    public function test_assertable_html(): void
    {
        $html = AssertableDocument::createFromString(<<<'HTML'
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

                <!-- Custom Element -->
                <my-web-component>I am a web component.</my-web-component>
            </body>
            </html>
        HTML);

        /*
        |--------------------------------------------------------------------------
        | With / Many / Elsewhere / Scope
        |--------------------------------------------------------------------------
        */

        $html->with('ul', function (AssertableElement $el): void {
            $el->assertElementsCount('li', 3);

            $el->with('li:nth-child(1)', fn (AssertableElement $el) => $el->assertAttributeEquals('id', 'foo'));
            $el->with('li:nth-child(2)', fn (AssertableElement $el) => $el->assertAttributeEquals('id', 'bar'));
            $el->with('li:nth-child(3)', fn (AssertableElement $el) => $el->assertAttributeEquals('id', 'baz'));

            $el->elsewhere('div', function (AssertableElement $el): void {
                $el->assertTextEquals('This is a test div.', true);
            });

            $el->many('li', function (AssertableElementsList $els): void {
                $els[0]->assertTextEquals('Foo');
                $els[1]->assertTextEquals('Bar');
                $els[2]->assertTextEquals('Baz');
            });

            $el->scope(function (AssertableElement $el): void {
                $el->assertClassMissing();
            });
        })->with('div', function (AssertableElement $el): void {
            $el->assertIdEquals('foo-bar');

            $el->elsewhere('ul', function (AssertableElement $el): void {
                $el->assertElementsCount('li[id]', 3);
            });
        })->many('p, span', function (AssertableElementsList $els): void {
            $els->assertCount(2);
            $els[0]->assertTag('p');
            $els[1]->assertTag('span');
        })->elsewhere('p', function (AssertableElement $el): void {
            $el->assertTextEquals('I am a test paragraph.');
        })->scope(function (AssertableDocument $doc): void {
            $doc->getElementById('foo-bar')->assertTextContains('This is a test div.');
        });

        /*
        |--------------------------------------------------------------------------
        | When
        |--------------------------------------------------------------------------
        */

        $html->when(true, function (AssertableDocument $doc): void {
            $doc->querySelectorAll('ul li')->assertCount(3);
        });

        $html->when(false, null, function (AssertableDocument $doc): void {
            $doc->querySelectorAll('ul li')->assertCount(3);
        });

        $html->querySelector('ul')->when(true, function (AssertableElement $el): void {
            $el->assertTextEquals('Foo Bar Baz', true);
        });

        $html->querySelector('ul')->when(true, null, function (AssertableElement $el): void {
            $el->assertTextEquals('Foo Bar Baz', true);
        });

        /*
        |--------------------------------------------------------------------------
        | Assertable HTML Element
        |--------------------------------------------------------------------------
        */

        $html->assertTitleEquals('Test Page Title');

        $html->querySelector('body')
            ->assertElementsExist('div')
            ->assertElementsDontExist('foo');

        $html->querySelector('div')
            ->assertElement(function (AssertableElement $el): bool {
                return
                    $el->id === 'foo-bar' &&
                    str_contains($el->attributes->value('data-bar'), 'baz') &&
                    $el->attributes->value('data-qux') === 'lux-pux';
            });

        $html->querySelector('div')
            ->assertMatchesSelector('div#foo-bar')
            ->assertDoesntMatchSelector('span[data-foo-bar]');

        $html->querySelector('ul')
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
            ->assertText(fn (AssertableText $text) => str_contains($text->value(true), 'is a test'));

        $html->querySelector('p')
            ->assertClassNotEmpty()
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
            ->assertClass(function (AssertableClassesList $classes): bool {
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
                    str_starts_with($attributes['id'], 'foo-') &&
                    str_ends_with($attributes['data-bar'], '-buz') &&
                    str_contains($attributes['data-qux'], 'x-p')
                ) && (
                    $attributes->has('id') &&
                    ! $attributes->has('foo-bar')
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

        /*
        |--------------------------------------------------------------------------
        | Assertable HTML Element (Short)
        |--------------------------------------------------------------------------
        */

        $html->querySelector('div')
            ->assert()
            ->element(fn (AssertableElement $element): bool => $element->tag === 'div')
            ->tag('div')
            ->text()
            ->text('This is a test div.')
            ->text(fn (AssertableText $text): bool => str_starts_with($text->value(true), 'This is a'))
            ->id()
            ->id('foo-bar')
            ->attr('data-bar')
            ->attr('data-bar', 'baz-buz')
            ->attr('data-qux', fn (?string $value): bool => str_contains($value, 'lux'));

        $html->querySelector('p')
            ->assert()
            ->class('lux');

        /*
        |--------------------------------------------------------------------------
        | Assertable HTML Element List
        |--------------------------------------------------------------------------
        */

        $html->querySelectorAll('ul li')
            ->assertCount(3)
            ->assertCountLessThan(4)
            ->assertCountLessThanOrEqual(4)
            ->assertCountGreaterThan(1)
            ->assertCountGreaterThanOrEqual(1)
            ->assertAny(fn (AssertableElement $el) => $el->matches('li'))
            ->assertAll(fn (AssertableElement $el) => $el->matches('li[id]'))
            ->assertElements(function (AssertableElementsList $els): bool {
                return $els[0]->matches('li[id="foo"]') && $els[1]->matches('li[id="bar"]') && $els[2]->matches('li[id="baz"]');
            });

        /*
        |--------------------------------------------------------------------------
        | Assertable Attributes List
        |--------------------------------------------------------------------------
        */

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
                    str_starts_with($attributes['id'], 'foo-') &&
                    str_ends_with($attributes['data-bar'], '-buz') &&
                    str_contains($attributes['data-qux'], 'x-p')
                ) && (
                    $attributes->has('id') &&
                    ! $attributes->has('foo-bar')
                );
            })
            ->assertAttribute('id', function (?string $value): bool {
                return $value === 'foo-bar';
            });

        /*
        |--------------------------------------------------------------------------
        | Assertable Classes List
        |--------------------------------------------------------------------------
        */

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
            ->assertClasses(function (AssertableClassesList $classes): bool {
                return $classes->contains('lux');
            });

        /*
        |--------------------------------------------------------------------------
        | Assertable Text
        |--------------------------------------------------------------------------
        */

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
                return
                    str_starts_with($text->value(), 'I') &&
                    str_ends_with($text->value(), '.') &&
                    str_contains($text->value(), 'test');
            });
    }
}
