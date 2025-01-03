<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Ziadoz\AssertableHtml\Prototype\AssertableHtmlDocument;
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
                <form method="get" action="/foo/bar" enctype="multipart/form-data">
                    <button type="submit">Save</button>
                </form>
                <ul id="list">
                    <li>Foo</li>
                    <li>Bar</li>
                    <li>Baz</li>
                </ul>
            </body>
            </html>
        HTML);

        dump(
            $html->querySelector('form'),
            $html->querySelector('form')->querySelectorAll('button'),
            $html->querySelector('form')->querySelector('button')->getElementsByTagName('li'),
            $html->querySelectorAll('li'),
            $html->getElementById('list'),
            $html->getElementsByTagName('li'),
        );

        $html->querySelector('form')->assertAttributeEquals('method', 'get');

        $html->with('form, ul', function ($els) {
            dump($els->nth(1));
            dump($els);
        });

        $html->with('form', function ($els) {
            dump($els);
        });

        $html->querySelector('form')->dump();
        dump($html->querySelector('form')->matches('form'));

        $html->querySelector('form')->when(true, function ($el) {
           dump($el);
        });

        dump($html->querySelector('button')->closest('form'));
    }
}
