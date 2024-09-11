<?php
namespace Ziadoz\AssertableHtml\Tests;

use Dom\HtmlDocument;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /**
     * Get the contents of a fixture file.
     */
    public function getFixture(string $file): string
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $file);
    }

    /**
     * Get the contents of a fixture file as an HTML document.
     */
    public function getFixtureHtml(string $file): HtmlDocument
    {
        return HtmlDocument::createFromString($this->getFixture($file));
    }
}
