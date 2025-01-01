<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests;

use Dom\Element;
use Dom\HtmlDocument;
use Dom\HTMLElement;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /** Get a string of HTML as an HTML document. */
    public function getTestElement(string $html, string $selector = 'body *:first-of-type'): HtmlElement|Element
    {
        return HtmlDocument::createFromString($html, LIBXML_NOERROR)->querySelector($selector);
    }
}
