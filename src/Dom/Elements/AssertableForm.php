<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom\Elements;

use Dom\Element;
use Dom\HTMLElement;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Contracts\PromotableAssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

readonly class AssertableForm extends AssertableElement implements PromotableAssertableElement
{
    /*
    |--------------------------------------------------------------------------
    | Interface
    |--------------------------------------------------------------------------
    */

    /** {@inheritDoc} */
    public static function shouldPromote(Element|HTMLElement $element): bool
    {
        return $element->tagName === 'FORM';
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Method
    |--------------------------------------------------------------------------
    */

    /** Assert the form has the given method attribute (GET or POST). */
    public function assertMethod(string $method, ?string $message = null): static
    {
        $method = trim(mb_strtolower($method));

        $this->isValidMethod($method);

        $this->assertAttribute(
            'method',
            fn (?string $value): bool => trim(mb_strtolower((string) $value)) === $method,
            $message ?? sprintf(
                "The form method doesn't equal [%s].",
                $method,
            ),
        );

        return $this;
    }

    /** Assert the form has the given hidden input method (PUT, PATCH or DELETE). */
    public function assertHiddenInputMethod(string $selector, string $method, ?string $message = null): static
    {
        $method = trim(mb_strtolower($method));

        $this->isValidHiddenInputMethod($method);

        $this->querySelector($selector)
            ->assertMatchesSelector('input[type="hidden"]')
            ->assertAttribute(
                'value',
                fn (?string $value): bool => trim(mb_strtolower((string) $value)) === $method,
                $message ?? sprintf(
                    "The form hidden input method doesn't equal [%s].",
                    $method,
                ),
            );

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Internal
    |--------------------------------------------------------------------------
    */

    /** Fail if the method isn't a valid form method. */
    protected function isValidMethod(string $method): void
    {
        if (! in_array($this->formatMethod($method), ['get', 'post', 'dialog'])) {
            PHPUnit::fail(sprintf("The method [%s] isn't a valid form method.", $method));
        }
    }

    /** Fail if the method isn't a valid hidden input method. */
    protected function isValidHiddenInputMethod(string $method): void
    {
        if (! in_array($method, ['put', 'patch', 'delete'])) {
            PHPUnit::fail(sprintf("The method [%s] isn't a valid form method.", $method));
        }
    }
}
