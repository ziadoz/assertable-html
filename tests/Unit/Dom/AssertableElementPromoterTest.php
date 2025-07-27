<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Dom;

use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableElementPromoter;
use Ziadoz\AssertableHtml\Dom\Elements\AssertableForm;

class AssertableElementPromoterTest extends TestCase
{
    #[DataProvider('promote_data_provider')]
    public function test_promote(string $html, ?string $class = null): void
    {
        $promoted = new AssertableElementPromoter($this->getElement($html))->promote();

        if (is_string($class)) {
            $this->assertInstanceOf($class, $promoted);
        } else {
            $this->assertNull($promoted);
        }
    }

    public static function promote_data_provider(): iterable
    {
        yield 'form' => ['<form></form>', AssertableForm::class];
        yield 'null' => ['<my-element></my-element>', null];
    }

    /*
    |--------------------------------------------------------------------------
    | Test Helper
    |--------------------------------------------------------------------------
    */

    private function getElement(string $html): HTMLElement|Element
    {
        return HTMLDocument::createFromString($html, LIBXML_HTML_NOIMPLIED)->querySelector('*:first-of-type');
    }
}
