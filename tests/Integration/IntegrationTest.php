<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

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

        //        dump(
        //            $html->querySelector('form'),
        //            $html->querySelector('form')->querySelectorAll('button'),
        //            $html->querySelector('form')->querySelector('button')->getElementsByTagName('li'),
        //            $html->querySelectorAll('li'),
        //            $html->getElementById('list'),
        //            $html->getElementsByTagName('li'),
        //        );
        //
        //        $html->querySelector('form')->assertAttributeEquals('method', 'get');
        //
        //        $html->with('form, ul', function ($els) {
        //            dump($els->nth(1));
        //            dump($els);
        //        });
        //
        //        $html->with('form', function ($els) {
        //            dump($els);
        //        });
        //
        //        $html->querySelector('form')->dump();
        //        dump($html->querySelector('form')->matches('form'));
        //
        //        $html->querySelector('form')->when(true, function ($el) {
        //            dump($el);
        //        });
        //
        //        dump($html->querySelector('button')->closest('form'));
        //        dump($html->querySelector('ul#list')->parentElement->innerHtml);
        //
        //        $html->querySelectorAll('ul li')->dump();
        //
        //        dump($html->querySelector('ul li:nth-of-type(2)')->previousElementSibling->innerHtml);
        //
        //        dump($html->documentElement->contains($html->querySelector('form')));
        //
        //        $html->querySelectorAll('ul li')->each(function ($el) {
        //            $el->assertAttributePresent('id');
        //        });
        //
        //        $lis = $html->querySelectorAll('ul li');
        //        dump($lis->first(), $lis->last());
        //
        //        dump($html->querySelector('p')->identifier());

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
