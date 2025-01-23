<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Ziadoz\AssertableHtml\AssertableHtmlServiceProvider;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

class TestResponseTest extends TestCase
{
    public function test_response(): void
    {
        $response = $this->getTestResponse();

        $response->assertsHtml(function (AssertableDocument $assertable): void {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
            $assertable->assertTitleEquals('Test Page Title');
        });

        $response->assertsHead(function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTag('head');
            $assertable->querySelector('meta[name="description"]')->assertAttributeEquals('content', 'Foo Bar');
        });

        $response->assertsBody(function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTag('body');
            $assertable->querySelector('p')->assertTextEquals('Foo');
        });
    }

    private function getTestResponse(): TestResponse
    {
        return TestResponse::fromBaseResponse(
            new Request(content: <<<'HTML'
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
            HTML),
        );
    }

    protected function getPackageProviders($app): array
    {
        return [AssertableHtmlServiceProvider::class];
    }
}
