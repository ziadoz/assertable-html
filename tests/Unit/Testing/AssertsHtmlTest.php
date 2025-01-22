<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Testing;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Testing\AssertsHtml;

class AssertsHtmlTest extends TestCase
{
    public function test_trait(): void
    {
        $case = new class
        {
            use AssertsHtml;
        };

        $html = <<<'HTML'
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Page Title</title>
            </head>
            <body>
                <p>Foo</p>
            </body>
            </html>
        HTML;

        $case->assertsHtml($html, function (AssertableDocument $assertable): void {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
        });

        $case->assertsHead($html, function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $this->assertSame('head', $assertable->tag);
        });

        $case->assertsBody($html, function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $this->assertSame('body', $assertable->tag);
        });
    }
}
