<?php
namespace Ziadoz\AssertHtml\Tests;

use Dom\HtmlDocument;
use Dom\HtmlElement;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertHtml\AssertableHtml;

class AssertableHtmlTest extends TestCase
{
    public function testInstance(): void
    {
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
            <head>
                <title>Page Title</title>
            </head>
            <body>
                <p>Paragraph</p>
            </body>
        </html>
        HTML;

        $assertable = new AssertableHtml(HtmlDocument::createFromString($html));
        $this->assertInstanceOf(HtmlDocument::class, $assertable->getDocument());
        $this->assertInstanceOf(HtmlElement::class, $assertable->getRoot());
        $this->assertSame('BODY', $assertable->getRoot()->tagName);
    }
}
