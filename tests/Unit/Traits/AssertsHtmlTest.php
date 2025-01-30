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
        $html = $this->getTestHtml();
        $case = $this->getTestClass();

        $case->assertableHtml($html)->scope(function (AssertableDocument $assertable) {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
        });

        $case->assertHtml($html, function (AssertableDocument $assertable): void {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
            $assertable->assertTitleEquals('Test Page Title');
        })->assertHead($html, function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTagEquals('head');
            $assertable->querySelector('meta[name="description"]')->assertAttributeEquals('content', 'Foo Bar');
        })->assertBody($html, function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTagEquals('body');
            $assertable->querySelector('p')->assertTextEquals('Foo');
        })->assertElement($html, 'p', function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTagEquals('p');
            $assertable->assertTextEquals('Foo');
        });
    }

    private function getTestClass(): object
    {
        return new class
        {
            use AssertsHtml;
        };
    }

    private function getTestHtml(): string
    {
        return <<<'HTML'
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
    }
}
