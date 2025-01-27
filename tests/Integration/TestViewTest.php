<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase;
use Ziadoz\AssertableHtml\AssertableHtmlServiceProvider;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

class TestViewTest extends TestCase
{
    use InteractsWithViews;

    public function test_view(): void
    {
        config()->set('view.paths', [__DIR__ . '/Fixtures']);

        $view = $this->view('view');

        $view->assertableElement()->scope(function (AssertableElement $assertable) {
            $this->assertInstanceOf(AssertableElement::class, $assertable);
        });

        $view->assertsElement(function (AssertableElement $assertable) {
            $assertable->querySelector('div')->assertIdEquals('component');

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
