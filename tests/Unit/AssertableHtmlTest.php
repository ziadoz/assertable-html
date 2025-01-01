<?php

namespace Ziadoz\AssertableHtml\Tests\Unit;

use Dom\HtmlDocument;
use Dom\HtmlElement;
use Ziadoz\AssertableHtml\AssertableHtml;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableHtmlTest extends TestCase
{
    public function test_instance(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertInstanceOf(HtmlDocument::class, $assertable->getDocument());
        $this->assertInstanceOf(HtmlElement::class, $assertable->getRoot());
        $this->assertSame('BODY', $assertable->getRoot()->tagName);
    }

    public function test_with_scoping(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertableOuter = $assertable->with('ul.outer');
        $this->assertSame('UL', $assertableOuter->getRoot()->tagName);
        $this->assertSame('outer', $assertableOuter->getRoot()->classList->value);

        $assertableInner = $assertableOuter->with('ul.inner');
        $this->assertSame('UL', $assertableInner->getRoot()->tagName);
        $this->assertSame('inner', $assertableInner->getRoot()->classList->value);
    }

    public function test_with_scoping_closure(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
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

    public function test_elsewhere_scoping(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('BODY', $assertable->getRoot()->tagName);

        $assertableInner = $assertable->with('ul.inner');
        $this->assertSame('UL', $assertableInner->getRoot()->tagName);
        $this->assertSame('inner', $assertableInner->getRoot()->classList->value);

        $assertableElsewhere = $assertable->elsewhere('ul.outer');
        $this->assertSame('UL', $assertableElsewhere->getRoot()->tagName);
        $this->assertSame('outer', $assertableElsewhere->getRoot()->classList->value);
    }

    public function test_elsewhere_scoping_closure(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
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

    public function test_get_document(): void
    {
        $assertable = new AssertableHtml($document = $this->getTestHtml(), 'body');
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

    public function test_get_root(): void
    {
        $assertable = new AssertableHtml($document = $this->getTestHtml(), 'body');
        $this->assertSame($document->querySelector('body'), $assertable->getRoot());

        $assertable->with('ul.inner', function (AssertableHtml $assertable) use ($document): void {
            $this->assertSame($document->querySelector('body ul.inner'), $assertable->getRoot());

            $assertable->with('li:first-of-type', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document->querySelector('body ul.inner li:first-of-type'), $assertable->getRoot());
            });

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document->querySelector('ul.outer'), $assertable->getRoot());
            });
        });
    }

    public function test_get_selector(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
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

    public function test_get_document_html(): void
    {
        $html = <<<'HTML'
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
        HTML;

        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertXmlStringEqualsXmlString($html, $assertable->getDocumentHtml());

        $assertable = new AssertableHtml($this->getTestHtml(), 'ul.outer');
        $this->assertXmlStringEqualsXmlString($html, $assertable->getDocumentHtml());
    }

    public function test_get_root_html(): void
    {
        // The indentation here is from HtmlDocument...
        $html = <<<'HTML'
        <ul class="outer">
          <li>Outer List Item 1</li>
          <li>
                    Outer List Item 2
                    <ul class="inner"><li>Inner List Item 1</li><li>Inner List Item 2</li></ul>
                </li>
        </ul>
        HTML;

        $assertable = new AssertableHtml($this->getTestHtml(), 'ul.outer');
        $this->assertXmlStringEqualsXmlString($html, $assertable->getRootHtml());

        $html = <<<'HTML'
        <ul class="inner">
          <li>Inner List Item 1</li>
          <li>Inner List Item 2</li>
        </ul>
        HTML;

        $assertable = new AssertableHtml($this->getTestHtml(), 'ul.inner');
        $this->assertXmlStringEqualsXmlString($html, $assertable->getRootHtml());
    }

    /** Get the contents of a fixture file as an HTML document. */
    public function getTestHtml(): HtmlDocument
    {
        return HtmlDocument::createFromString(<<<'HTML'
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
        HTML);
    }
}
