<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Ziadoz\AssertableHtml\Mixins\TestResponseMixins;

class AssertableHtmlServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            TestResponse::mixin(new TestResponseMixins);
        }
    }
}
