<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Illuminate\Testing\TestView;
use Ziadoz\AssertableHtml\Mixins\TestResponseMixins;
use Ziadoz\AssertableHtml\Mixins\TestViewMixins;

class AssertableHtmlServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            TestResponse::mixin(new TestResponseMixins);
            TestView::mixin(new TestViewMixins);
        }
    }
}
