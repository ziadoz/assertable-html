<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Ziadoz\AssertableHtml\Prototype\Dom\AssertableHtmlDocument;
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
                <p>I am a test paragraph.</p>

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
//        dump($html->querySelector('ul'));
    }
}
