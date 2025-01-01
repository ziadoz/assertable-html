<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Matchers;

use Dom\Document;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\HTMLElement;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Support\Utilities;

class RootElementMatcher
{
    /** Determine the root element to perform assertions on. The root can only ever be a single element. */
    public function match(HtmlDocument|Document|HtmlElement|Element $document, string $selector = ''): HtmlElement|Element
    {
        $nodes = $document->querySelectorAll($selector);

        if (count($nodes) !== 1) {
            PHPUnit::fail(
                trim(sprintf(
                    "The selector [%s] matches %d elements instead of exactly 1 element.\n\n%s",
                    $selector,
                    count($nodes),
                    Utilities::nodesToMatchesHtml($nodes),
                )),
            );
        }

        return $nodes[0];
    }
}
