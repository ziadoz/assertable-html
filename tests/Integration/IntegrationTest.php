<?php

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Dom\HTMLDocument;
use Ziadoz\AssertableHtml\AssertableHtml;
use Ziadoz\AssertableHtml\Tests\TestCase;

class IntegrationTest extends TestCase
{
    public function test_assertable_html(): void
    {
        $html = new AssertableHtml($this->getTestHtml(), 'html');
        $html->with('form', function (AssertableHtml $form) {
            $form->assertAttributeEquals('method', 'get');
            $form->assertAttributeEquals('enctype', 'multipart/form-data');
        });
    }

    /** Get the contents of a fixture file as an HTML document. */
    public function getTestHtml(): HtmlDocument
    {
        return HtmlDocument::createFromString(<<<'HTML'
        <!DOCTYPE html>
        <html>
        <head>
            <title>Test Page Title</title>
        </head>
        <body>
            <form method="get" enctype="multipart/form-data">
                <button type="submit">Save</button>
            </form>
        </body>
        </html>
        HTML);
    }
}
