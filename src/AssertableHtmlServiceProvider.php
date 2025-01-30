<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Testing\TestComponent;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\TestView;
use Ziadoz\AssertableHtml\Mixins\TestComponentMixins;
use Ziadoz\AssertableHtml\Mixins\TestResponseMixins;
use Ziadoz\AssertableHtml\Mixins\TestViewMixins;

class AssertableHtmlServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningUnitTests()) {
            TestResponse::mixin(new TestResponseMixins);
            TestView::mixin(new TestViewMixins);

            // This functionality is only available in the point release after I added it to Laravel.
            // @see: https://github.com/laravel/framework/pull/54359
            if (in_array(Macroable::class, class_uses(TestComponent::class) ?? [])) {
                TestComponent::mixin(new TestComponentMixins);
            }
        }
    }
}
