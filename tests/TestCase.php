<?php

namespace Ziadoz\AssertableHtml\Tests;

use Dom\Element;
use Dom\HtmlDocument;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /** Get a string of HTML as an HTML document. */
    public function getTestElement(string $html, string $selector = 'body *:first-of-type'): Element
    {
        return HtmlDocument::createFromString($html, LIBXML_NOERROR)->querySelector($selector);
    }
}
