<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom\Elements;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Concerns\AssertsMany;
use Ziadoz\AssertableHtml\Contracts\PromotableAssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

readonly class AssertableForm extends AssertableElement implements PromotableAssertableElement
{
    use AssertsMany;

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

    /** Assert the form has the given method attribute. */
    public function assertMethod(string $method, ?string $message = null): static
    {
        $method = trim(mb_strtolower($method));

        $this->assertAttribute(
            'method',
            fn (?string $value): bool => trim(mb_strtolower((string) $value)) === $method,
            $message ?? sprintf("The form method doesn't equal [%s].", $method),
        );

        return $this;
    }

    /** Assert the form has the GET method attribute. */
    public function assertMethodGet(?string $message = null): static
    {
        $this->assertMethod('get', $message);

        return $this;
    }

    /** Assert the form has the POST method attribute. */
    public function assertMethodPost(?string $message = null): static
    {
        $this->assertMethod('post', $message);

        return $this;
    }

    /** Assert the form has the DIALOG method attribute. */
    public function assertMethodDialog(?string $message = null): static
    {
        $this->assertMethod('dialog', $message);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Hidden Method
    |--------------------------------------------------------------------------
    */

    /** Assert the form has the given hidden input method. */
    public function assertHiddenInputMethod(string $selector, string $method, ?string $message = null): static
    {
        $this->assertMany(function () use ($selector, $method): void {
            $method = trim(mb_strtolower($method));

            $this->querySelector($selector)
                ->assertMatchesSelector('input[type="hidden"]')
                ->assertAttribute('value', fn (?string $value): bool => trim(mb_strtolower((string) $value)) === $method);
        }, $message ?? sprintf("The form hidden input method doesn't equal [%s].", $method));

        return $this;
    }

    /** Assert the form has the PUT hidden input method. */
    public function assertMethodPut(?string $message = null): static
    {
        $this->assertHiddenInputMethod('input[type="hidden"][name="_method"]', 'put', $message);

        return $this;
    }

    /** Assert the form has the PATCH hidden input method. */
    public function assertMethodPatch(?string $message = null): static
    {
        $this->assertHiddenInputMethod('input[type="hidden"][name="_method"]', 'patch', $message);

        return $this;
    }

    /** Assert the form has the DELETE hidden input method. */
    public function assertMethodDelete(?string $message = null): static
    {
        $this->assertHiddenInputMethod('input[type="hidden"][name="_method"]', 'delete', $message);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Upload
    |--------------------------------------------------------------------------
    */

    /** Assert the form accepts uploads (has correct enctype and at least one file input. */
    public function assertAcceptsUpload(?string $message = null): static
    {
        $this->assertMany(function (): void {
            $this->assertAttribute('enctype', fn (?string $value): bool => trim(mb_strtolower((string) $value)) === 'multipart/form-data')
                ->assertElementsCountGreaterThanOrEqual('input[type="file"]', 1);
        }, $message ?? "The form doesn't accept uploads.");

        return $this;
    }
}
