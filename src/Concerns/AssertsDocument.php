<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Concerns;

use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsDocument
{
    /*
    |--------------------------------------------------------------------------
    | Assert Title
    |--------------------------------------------------------------------------
    */

    /** Assert the page title equals the given value. */
    public function assertTitleEquals(string $title, ?string $message = null): self
    {
        PHPUnit::assertSame(
            $title,
            $this->title,
            $message ?? "The page title doesn't equal the given title.",
        );

        return $this;
    }
}
