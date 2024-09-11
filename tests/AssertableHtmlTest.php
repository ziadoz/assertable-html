<?php
namespace Ziadoz\AssertableHtml\Tests;

use Dom\HtmlDocument;
use Dom\HtmlElement;
use PHPUnit\Framework\ExpectationFailedException;
use Ziadoz\AssertableHtml\AssertableHtml;

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
        $this->expectExceptionMessage('The root selector [p] matches 2 elements instead of exactly 1 element.');

        new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'p');
    }

    public function testWithScoping(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'body');
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
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertable->with('ul.outer', function (AssertableHtml $assertable): void {
            $this->assertSame('UL', $assertable->getRoot()->tagName);
            $this->assertSame('outer', $assertable->getRoot()->classList->value);

            $assertable->with('ul.inner', function (AssertableHtml $assertable): void {
                $this->assertSame('UL', $assertable->getRoot()->tagName);
                $this->assertSame('inner', $assertable->getRoot()->classList->value);
            });
        });
    }

    public function testElsewhereScoping(): void
    {
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
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
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertable->with('ul.inner', function (AssertableHtml $assertable): void {
            $this->assertSame('UL', $assertable->getRoot()->tagName);
            $this->assertSame('inner', $assertable->getRoot()->classList->value);

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable): void {
                $this->assertSame('UL', $assertable->getRoot()->tagName);
                $this->assertSame('outer', $assertable->getRoot()->classList->value);
            });
        });
    }

    public function testGetDocument(): void
    {
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame($document, $assertable->getDocument());

        $assertable->with('ul.inner', function (AssertableHtml $assertable) use ($document): void {
            $this->assertSame($document, $assertable->getDocument());

            $assertable->with('li:first-of-type', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document, $assertable->getDocument());
            });

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document, $assertable->getDocument());
            });
        });
    }

    public function testGetRoot(): void
    {
        $this->markTestIncomplete('@todo');
    }

    public function testGetSelector(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame('body', $assertable->getSelector());

        $assertable->with('ul.inner', function (AssertableHtml $assertable): void {
            $this->assertSame('body ul.inner', $assertable->getSelector());

            $assertable->with('li:first-of-type', function (AssertableHtml $assertable): void {
                $this->assertSame('body ul.inner li:first-of-type', $assertable->getSelector());
            });

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable): void {
                $this->assertSame('ul.outer', $assertable->getSelector());
            });
        });
    }

    public function testGetDocumentHtml(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertXmlStringEqualsXmlString(
            <<<'HTML'
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Page Title</title>
            </head>
            <body>
                <p>Test Paragraph 1</p>
                <p>Test Paragraph 2</p>
                <ul class="outer">
                    <li>Outer List Item 1</li>
                    <li>
                        Outer List Item 2
                        <ul class="inner">
                            <li>Inner List Item 1</li>
                            <li>Inner List Item 2</li>
                        </ul>
                    </li>
                </ul>
            </body>
            </html>
            HTML,
            $assertable->getDocumentHtml(),
        );
    }
    public function testGetRootHtml(): void
    {
        $assertable = new AssertableHtml($this->getFixtureHtml('skeleton.html'), 'ul.outer');
        $this->assertXmlStringEqualsXmlString(
            // The indentation here is down to HtmlDocument...
            <<<'HTML'
            <ul class="outer">
              <li>Outer List Item 1</li>
              <li>
                        Outer List Item 2
                        <ul class="inner"><li>Inner List Item 1</li><li>Inner List Item 2</li></ul>
                    </li>
            </ul>
            HTML,
            $assertable->getRootHtml(),
        );
    }
}
