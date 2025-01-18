<?php

namespace Ziadoz\AssertableHtml\Dom\Elements;

use Ziadoz\AssertableHtml\Dom\AssertableHtmlElement;

readonly class AssertableHtmlFormElement extends AssertableHtmlElement
{
    public function assertMethodGet(?string $message = null): static
    {
        return $this->assertMethod('get', $message);
    }

    public function assertMethodPost(?string $message = null): static
    {
        return $this->assertMethod('post', $message);
    }

    private function assertMethod(string $method, ?string $message = null): static
    {
        $this->attributes->assertAttribute('method', function (?string $value) use ($method): bool {
            return trim(strtolower($this->attributes['method'] ?? '')) === $method;
        }, $message ?? sprintf(
            "The form element [%s] method isn't %s.",
            $this->identifier(),
            strtoupper($method),
        ));

        return $this;
    }

    public function assertAcceptsUploads(?string $message = null): static
    {
        $this->attributes->assertAttribute('enctype', function (?string $value): bool {
            return trim(strtolower($value)) === 'multipart/form-data';
        }, $message ?? sprintf(
            "The form element [%s] enctype isn't multipart/form-data.",
            $this->identifier(),
        ));

        return $this;
    }

    // @todo: Laravel
    // _method => PUT, PATCH, DELETE
}
