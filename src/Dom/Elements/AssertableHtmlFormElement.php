<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom\Elements;

use Ziadoz\AssertableHtml\Dom\AssertableHtmlElement;

readonly class AssertableHtmlFormElement extends AssertableHtmlElement
{
    /** Assert the form's method is GET. */
    public function assertMethodGet(?string $message = null): static
    {
        return $this->assertMethod('get', $message);
    }

    /** Assert the form's method is POST. */
    public function assertMethodPost(?string $message = null): static
    {
        return $this->assertMethod('post', $message);
    }

    /** Assert the form's method is PUT (via hidden _method input). */
    public function assertMethodPut(?string $message = null): static
    {
        return $this->assertHiddenInputMethod('put', $message);
    }

    /** Assert the form's method is PATCH (via hidden _method input). */
    public function assertMethodPatch(?string $message = null): static
    {
        return $this->assertHiddenInputMethod('patch', $message);
    }

    /** Assert the form's method is DELETE (via hidden _method input). */
    public function assertMethodDelete(?string $message = null): static
    {
        return $this->assertHiddenInputMethod('delete', $message);
    }

    /** Assert the form's method matches the given value. */
    private function assertMethod(string $method, ?string $message = null): static
    {
        $this->attributes->assertAttribute('method', function (?string $value) use ($method): bool {
            return trim(strtolower($this->attributes['method'] ?? '')) === $method;
        }, $message ?? sprintf(
            "The element [%s] method isn't %s.",
            $this->identifier(),
            strtoupper($method),
        ));

        return $this;
    }

    /** Assert the form's hidden _method input matches the given value. */
    private function assertHiddenInputMethod(string $method, ?string $message = null): static
    {
        $this->assertIfNull($this->querySelector('input[type="hidden"][name="_method"]'), sprintf(
            "The element [%s] doesn't contain a hidden _method input.",
            $this->identifier(),
        ))->assertAttribute('value', function (?string $value) use ($method): bool {
            return trim(strtolower($value)) === $method;
        }, $message ?? sprintf(
            "The element [%s] doesn't contain a %s hidden _method input.",
            $this->identifier(),
            strtoupper($method),
        ));

        return $this;
    }

    /** Assert the form's enctype attribute is multipart/form-data. */
    public function assertAcceptsUploads(?string $message = null): static
    {
        $this->attributes->assertAttribute('enctype', function (?string $value): bool {
            return trim(strtolower($value)) === 'multipart/form-data';
        }, $message ?? sprintf(
            "The element [%s] enctype isn't multipart/form-data.",
            $this->identifier(),
        ));

        return $this;
    }

    /** Assert the form's action matches the given value. */
    public function assertAction(string $value, bool $normaliseWhitespace = false, ?string $message = null): static
    {
        $this->attributes->assertEquals('action', $value, $normaliseWhitespace, $message ?? sprintf(
            "The element [%s] action doesn't match the given value.",
            $this->identifier(),
        ));

        return $this;
    }
}
