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
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'body');
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

    public function testWithScoping(): void
    {
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);
        $this->assertSame($document, $assertable->getDocument());

        $assertableOuter = $assertable->with('ul.outer');
        $this->assertSame('UL', $assertableOuter->getRoot()->tagName);
        $this->assertSame('outer', $assertableOuter->getRoot()->classList->value);
        $this->assertSame($document, $assertableOuter->getDocument());

        $assertableInner = $assertableOuter->with('ul.inner');
        $this->assertSame('UL', $assertableInner->getRoot()->tagName);
        $this->assertSame('inner', $assertableInner->getRoot()->classList->value);
        $this->assertSame($document, $assertableInner->getDocument());
    }

    public function testWithScopingClosure(): void
    {
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);
        $this->assertSame($document, $assertable->getDocument());

        $assertable->with('ul.outer', function (AssertableHtml $assertableOuter) use ($document): void {
            $this->assertSame('UL', $assertableOuter->getRoot()->tagName);
            $this->assertSame('outer', $assertableOuter->getRoot()->classList->value);
            $this->assertSame($document, $assertableOuter->getDocument());

            $assertableOuter->with('ul.inner', function (AssertableHtml $assertableInner) use ($document): void {
                $this->assertSame('UL', $assertableInner->getRoot()->tagName);
                $this->assertSame('inner', $assertableInner->getRoot()->classList->value);
                $this->assertSame($document, $assertableInner->getDocument());
            });
        });
    }

    public function testElsewhereScoping(): void
    {
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);
        $this->assertSame($document, $assertable->getDocument());

        $assertableInner = $assertable->with('ul.inner');
        $this->assertSame('UL', $assertableInner->getRoot()->tagName);
        $this->assertSame('inner', $assertableInner->getRoot()->classList->value);
        $this->assertSame($document, $assertableInner->getDocument());

        $assertableElsewhere = $assertable->elsewhere('ul.outer');
        $this->assertSame('UL', $assertableElsewhere->getRoot()->tagName);
        $this->assertSame('outer', $assertableElsewhere->getRoot()->classList->value);
        $this->assertSame($document, $assertableElsewhere->getDocument());
    }

    public function testElsewhereScopingClosure(): void
    {
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);
        $this->assertSame($document, $assertable->getDocument());

        $assertable->with('ul.inner', function (AssertableHtml $assertableInner) use ($document): void {
            $this->assertSame('UL', $assertableInner->getRoot()->tagName);
            $this->assertSame('inner', $assertableInner->getRoot()->classList->value);
            $this->assertSame($document, $assertableInner->getDocument());

            $assertableInner->elsewhere('ul.outer', function (AssertableHtml $assertableElsewhere) use ($document): void {
                $this->assertSame('UL', $assertableElsewhere->getRoot()->tagName);
                $this->assertSame('outer', $assertableElsewhere->getRoot()->classList->value);
                $this->assertSame($document, $assertableElsewhere->getDocument());
            });
        });
    }
}
