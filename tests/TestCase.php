<?php
namespace Ziadoz\AssertableHtml\Tests;

use Dom\HtmlDocument;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /** Get the contents of a fixture file as an HTML document. */
    public function getFixtureHtml(string $file): HtmlDocument
    {
        return HtmlDocument::createFromString(file_get_contents(__DIR__ . '/Fixtures/' . $file));
    }
}
