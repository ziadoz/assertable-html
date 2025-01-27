<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase;
use Ziadoz\AssertableHtml\AssertableHtmlServiceProvider;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

class TestViewTest extends TestCase
{
    use InteractsWithViews;

    public function test_view(): void
    {
        config()->set('view.paths', [__DIR__ . '/Fixtures']);

        $view = $this->view('view');

        // Assertable Element
        $assertable = $view->assertableElement();
        $assertable->querySelector('div')->assertIdEquals('component');

        // Assert Element
        $view->assertElement(function (AssertableDocument $assertable) {
            $assertable->querySelector('div')->assertIdEquals('component');
        })->assertElement(function (AssertableDocument $assertable) {
            $lis = $assertable->querySelectorAll('li')
                ->assertCount(4)
                ->assertAll(function (AssertableElement $el): bool {
                    return $el->classes->contains('bullet-point');
                });

            $lis[0]->assertIdEquals('foo')->assertTextContains('Foo');
            $lis[1]->assertIdEquals('bar')->assertTextContains('Bar');
            $lis[2]->assertIdEquals('baz')->assertTextContains('Baz');
            $lis[3]->assertIdEquals('qux')->assertTextContains('Qux');
        });
    }

    protected function getPackageProviders($app): array
    {
        return [AssertableHtmlServiceProvider::class];
    }
}
