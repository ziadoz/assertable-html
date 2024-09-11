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

    public function testGetDocument(): void
    {
        $assertable = new AssertableHtml($document = $this->getFixtureHtml('skeleton.html'), 'body');
        $this->assertSame($document, $assertable->getDocument());

        $assertable->with('ul.inner', function (AssertableHtml $assertableInner) use ($document): void {
            $this->assertSame($document, $assertableInner->getDocument());

            $assertableInner->elsewhere('ul.outer', function (AssertableHtml $assertableElsewhere) use ($document): void {
                $this->assertSame($document, $assertableElsewhere->getDocument());
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
