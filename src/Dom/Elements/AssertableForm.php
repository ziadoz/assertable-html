<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Dom\Elements;

use Dom\Element;
use Dom\HTMLElement;
use Ziadoz\AssertableHtml\Contracts\PromotableAssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

readonly class AssertableForm extends AssertableElement implements PromotableAssertableElement
{
    public static function shouldPromote(Element|HTMLElement $element): bool
    {
        return $element->tagName === 'FORM';
    }
}
