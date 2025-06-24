<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration\Laravel;

use Closure;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ziadoz\AssertableHtml\AssertableHtmlServiceProvider;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

class TestResponseTest extends TestCase
{
    use MakesHttpRequests;

    #[DataProvider('response_data_provider')]
    public function test_response(Closure $makeResponse): void
    {
        Route::get('/', $makeResponse);

        $response = $this->get('/');

        // Assertable HTML
        $assertable = $response->assertableHtml();
        $assertable->assertTitleEquals('Test Page Title');

        // Assert HTML, Head, Body, Element
        $response->assertHtml(function (AssertableDocument $assertable): void {
            $assertable->assertTitleEquals('Test Page Title');
        })->assertHead(function (AssertableElement $assertable): void {
            $assertable->assertTagEquals('head');
            $assertable->querySelector('meta[name="description"]')->assertAttributeEquals('content', 'Foo Bar');
        })->assertBody(function (AssertableElement $assertable): void {
            $assertable->assertTagEquals('body');
            $assertable->querySelector('p')->assertTextEquals('Foo');
        })->assertElement('p', function (AssertableElement $assertable): void {
            $assertable->assertTagEquals('p');
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

    private static function getTestResponse(): \Illuminate\Http\Response
    {
        return Response::make(<<<'HTML'
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
        HTML);
    }

    private static function getTestStreamedResponse(): StreamedResponse
    {
        return Response::stream(function () {
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
        });
    }

    protected function getPackageProviders($app): array
    {
        return [AssertableHtmlServiceProvider::class];
    }
}
