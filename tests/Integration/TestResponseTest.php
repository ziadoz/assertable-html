<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Ziadoz\AssertableHtml\AssertableHtmlServiceProvider;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

class TestResponseTest extends TestCase
{
    #[DataProvider('response_data_provider')]
    public function test_response(Closure $makeResponse): void
    {
        $response = $makeResponse();

        $response->assertableHtml()->scope(function (AssertableDocument $assertable) {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
        });

        $response->assertHtml(function (AssertableDocument $assertable): void {
            $this->assertInstanceOf(AssertableDocument::class, $assertable);
            $assertable->assertTitleEquals('Test Page Title');
        })->assertHead(function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTag('head');
            $assertable->querySelector('meta[name="description"]')->assertAttributeEquals('content', 'Foo Bar');
        })->assertBody(function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTag('body');
            $assertable->querySelector('p')->assertTextEquals('Foo');
        })->assertElement('p', function (AssertableElement $assertable): void {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
            $assertable->assertTag('p');
            $assertable->assertTextEquals('Foo');
        });
    }

    public static function response_data_provider(): iterable
    {
        yield 'response' => [
            static::getTestResponse(...),
        ];

        yield 'streamed response' => [
            static::getTestStreamedResponse(...),
        ];
    }

    private static function getTestResponse(): TestResponse
    {
        return TestResponse::fromBaseResponse(
            Response::make(<<<'HTML'
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

    private static function getTestStreamedResponse(): TestResponse
    {
        return TestResponse::fromBaseResponse(
            Response::stream(function () {
                echo <<< 'TOP'
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Test Page Title</title>
                     <meta name="description" content="Foo Bar">
                </head>
                TOP;
                ob_flush();

                echo <<<'BOTTOM'
                <body>
                    <p>Foo</p>
                </body>
                </html>
                BOTTOM;
                ob_flush();

                flush();
            }),
        );
    }

    protected function getPackageProviders($app): array
    {
        return [AssertableHtmlServiceProvider::class];
    }
}
