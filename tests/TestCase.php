<?php

namespace Ziadoz\AssertableHtml\Tests;

use Dom\HtmlDocument;
use Dom\HtmlElement;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /** Get the contents of a fixture file as an HTML document. */
    public function getFixtureHtml(string $file): HtmlDocument
    {
        return HtmlDocument::createFromString(file_get_contents(__DIR__ . '/Fixtures/' . $file));
    }

    /** Get a string of HTML as an HTML document. */
    public function getFixtureElement(string $html): HtmlElement
    {
        return HtmlDocument::createFromString($html, LIBXML_NOERROR)->querySelector('body *:first-of-type');
    }
}
