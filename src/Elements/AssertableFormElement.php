<?php

namespace Ziadoz\AssertableHtml\Elements;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Contracts\MatchableInterface;

class AssertableFormElement extends AssertableElement implements MatchableInterface
{
    /** Return if the given element matches. */
    public static function matches(HTMLElement|Element $element): bool
    {
        return $element->tagName === 'FORM';
    }

    /** Assert the form's method is GET. */
    public function assertMethodGet(): static
    {
        $this->assertAttributeIsAllowed('method', 'get');

        return $this;
    }

    /** Assert the form's method is POST. */
    public function assertMethodPost(): static
    {
        $this->assertAttributeIsAllowed('method', 'post');

        return $this;
    }

    /** Assert the form's action equals the given value. */
    public function assertActionEquals(string $action): static
    {
        $this->assertAttributeEquals('action', $action);

        return $this;
    }

    /** Assert the form's accepts uploads. */
    public function assertAcceptsUploads(): static
    {
        $this->assertAttributeIsAllowed('enctype', 'multipart/form-data');

        return $this;
    }
}
