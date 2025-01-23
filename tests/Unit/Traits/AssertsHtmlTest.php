<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Traits\AssertsHtml;

class AssertsHtmlTest extends TestCase
{
    public function test_trait(): void
    {
        $html = <<<'HTML'
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Page Title</title>
                <meta name="description" content="Foo Bar">
            </head>
            <body>
                <p>Foo</p>
            </body>
            </html>
        HTML;

        $case = $this->getTestClass();

        $case->assertableDocument($html)->scope(function (AssertableDocument $assertable) {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
        });

        $case->assertHtml($html, function (AssertableDocument $assertable): void {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
            $assertable->assertTitleEquals('Test Page Title');
        });

        $case->assertHead($html, function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTag('head');
            $assertable->querySelector('meta[name="description"]')->assertAttributeEquals('content', 'Foo Bar');
        });

        $case->assertBody($html, function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTag('body');
            $assertable->querySelector('p')->assertTextEquals('Foo');
        });
    }

    private function getTestClass(): object
    {
        return new class
        {
            use AssertsHtml;
        };
    }
}
