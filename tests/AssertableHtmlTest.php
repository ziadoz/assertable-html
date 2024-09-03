<?php
namespace Ziadoz\AssertHtml\Tests;

use Dom\HtmlDocument;
use Dom\HtmlElement;
use PHPUnit\Framework\ExpectationFailedException;
use Ziadoz\AssertHtml\AssertableHtml;

class AssertableHtmlTest extends TestCase
{
    public function testInstance(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'));
        $this->assertInstanceOf(HtmlDocument::class, $assertable->getDocument());
        $this->assertInstanceOf(HtmlElement::class, $assertable->getRoot());
        $this->assertSame('BODY', $assertable->getRoot()->tagName);
    }

    public function testDetermineRootMatchesMultipleElements(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The selector [p] matches 2 elements instead of exactly 1 element.');

        new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'p');
    }
}
