<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration\Laravel\Fixtures;

use Illuminate\View\Component as LaravelComponent;
use Illuminate\View\View;

class Component extends LaravelComponent
{
    public function __construct(
        public bool $showFooLink = true,
        public bool $showBarLink = true,
        public bool $showBazLink = true,
    ) {
    }

    public function render(): View
    {
        return view('component');
    }
}
