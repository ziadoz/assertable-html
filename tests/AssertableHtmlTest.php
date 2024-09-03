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

    public function testWithScoping(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'));
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertableOuter = $assertable->with('ul.outer');
        $this->assertSame('UL', $assertableOuter->getRoot()->tagName);
        $this->assertSame('outer', $assertableOuter->getRoot()->classList->value);

        $assertableInner = $assertableOuter->with('ul.inner');
        $this->assertSame('UL', $assertableInner->getRoot()->tagName);
        $this->assertSame('inner', $assertableInner->getRoot()->classList->value);
    }

    public function testWithScopingClosure(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'));
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertable->with('ul.outer', function (AssertableHtml $assertableOuter): void {
            $this->assertSame('UL', $assertableOuter->getRoot()->tagName);
            $this->assertSame('outer', $assertableOuter->getRoot()->classList->value);

            $assertableOuter->with('ul.inner', function (AssertableHtml $assertableInner): void {
                $this->assertSame('UL', $assertableInner->getRoot()->tagName);
                $this->assertSame('inner', $assertableInner->getRoot()->classList->value);
            });
        });
    }

    public function testElsewhereScoping(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'));
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertableInner = $assertable->with('ul.inner');
        $this->assertSame('UL', $assertableInner->getRoot()->tagName);
        $this->assertSame('inner', $assertableInner->getRoot()->classList->value);

        $assertableElsewhere = $assertable->elsewhere('ul.outer');
        $this->assertSame('UL', $assertableElsewhere->getRoot()->tagName);
        $this->assertSame('outer', $assertableElsewhere->getRoot()->classList->value);
    }

    public function testElsewhereScopingClosure(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'));
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertable->with('ul.inner', function (AssertableHtml $assertableInner): void {
            $this->assertSame('UL', $assertableInner->getRoot()->tagName);
            $this->assertSame('inner', $assertableInner->getRoot()->classList->value);

            $assertableInner->elsewhere('ul.outer', function (AssertableHtml $assertableElsewhere): void {
                $this->assertSame('UL', $assertableElsewhere->getRoot()->tagName);
                $this->assertSame('outer', $assertableElsewhere->getRoot()->classList->value);
            });
        });
    }
}
