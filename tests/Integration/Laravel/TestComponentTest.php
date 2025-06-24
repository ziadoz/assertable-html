<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration\Laravel;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase;
use Ziadoz\AssertableHtml\AssertableHtmlServiceProvider;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Tests\Integration\Laravel\Fixtures\Component;

class TestComponentTest extends TestCase
{
    use InteractsWithViews;

    public function test_view(): void
    {
        config()->set('view.paths', [__DIR__ . '/Fixtures']);

        $component = $this->component(Component::class);

        // Assertable Element
        $assertable = $component->assertableHtml();
        $assertable->querySelector('nav')->assertIdEquals('component');

        // Assert Element
        $component->assertComponent(function (AssertableDocument $assertable) {
            $assertable->querySelector('nav')->assertIdEquals('component');
        })->assertComponent(function (AssertableDocument $assertable) {
            $lis = $assertable->querySelectorAll('a')
                ->assertCount(3)
                ->assertAll(function (AssertableElement $el): bool {
                    return $el->classes->contains('nav-link');
                });

            $lis[0]->assertIdEquals('foo')->assertTextContains('Foo');
            $lis[1]->assertIdEquals('bar')->assertTextContains('Bar');
            $lis[2]->assertIdEquals('baz')->assertTextContains('Baz');
        })->assertElement('nav', function (AssertableElement $assertable) {
            $assertable->querySelectorAll('a')->assertCount(3);
        });
    }

    protected function getPackageProviders($app): array
    {
        return [AssertableHtmlServiceProvider::class];
    }
}
