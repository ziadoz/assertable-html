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
        $this->assertInstanceOf(HtmlDocument::class, $assertable->document);
        $this->assertInstanceOf(HtmlElement::class, $assertable->root);
        $this->assertSame('body', $assertable->selector);
        $this->assertSame('BODY', $assertable->root->tagName);
    }

    public function test_with_scoping(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('BODY', $assertable->root->tagName);

        $assertableOuter = $assertable->with('ul.outer');
        $this->assertSame('UL', $assertableOuter->root->tagName);
        $this->assertSame('outer', $assertableOuter->root->classList->value);

        $assertableInner = $assertableOuter->with('ul.inner');
        $this->assertSame('UL', $assertableInner->root->tagName);
        $this->assertSame('inner', $assertableInner->root->classList->value);
    }

    public function test_with_scoping_closure(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('BODY', $assertable->root->tagName);

        $assertable->with('ul.outer', function (AssertableHtml $assertable): void {
            $this->assertSame('UL', $assertable->root->tagName);
            $this->assertSame('outer', $assertable->root->classList->value);

            $assertable->with('ul.inner', function (AssertableHtml $assertable): void {
                $this->assertSame('UL', $assertable->root->tagName);
                $this->assertSame('inner', $assertable->root->classList->value);
            });
        });
    }

    public function test_elsewhere_scoping(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('BODY', $assertable->root->tagName);

        $assertableInner = $assertable->with('ul.inner');
        $this->assertSame('UL', $assertableInner->root->tagName);
        $this->assertSame('inner', $assertableInner->root->classList->value);

        $assertableElsewhere = $assertable->elsewhere('ul.outer');
        $this->assertSame('UL', $assertableElsewhere->root->tagName);
        $this->assertSame('outer', $assertableElsewhere->root->classList->value);
    }

    public function test_elsewhere_scoping_closure(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('BODY', $assertable->root->tagName);

        $assertable->with('ul.inner', function (AssertableHtml $assertable): void {
            $this->assertSame('UL', $assertable->root->tagName);
            $this->assertSame('inner', $assertable->root->classList->value);

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable): void {
                $this->assertSame('UL', $assertable->root->tagName);
                $this->assertSame('outer', $assertable->root->classList->value);
            });
        });
    }

    public function test_when(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('Foo', $assertable->when(true, 'Foo', 'Bar'));
        $this->assertSame('Bar', $assertable->when(false, 'Foo', 'Bar'));
        $this->assertSame('Foo', $assertable->when(fn () => true, 'Foo', 'Bar'));
        $this->assertSame('Bar', $assertable->when(fn () => false, 'Foo', 'Bar'));
        $this->assertSame($assertable, $assertable->when(true, fn (AssertableHtml $assertable): AssertableHtml => $assertable), 'Bar');
        $this->assertSame($assertable, $assertable->when(false, 'Foo', fn (AssertableHtml $assertable): AssertableHtml => $assertable));
    }

    public function test_get_document(): void
    {
        $assertable = new AssertableHtml($document = $this->getTestHtml(), 'body');
        $this->assertSame($document, $assertable->document);

        $assertable->with('ul.inner', function (AssertableHtml $assertable) use ($document): void {
            $this->assertSame($document, $assertable->document);

            $assertable->with('li:first-of-type', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document, $assertable->document);
            });

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document, $assertable->document);
            });
        });
    }

    public function test_get_root(): void
    {
        $assertable = new AssertableHtml($document = $this->getTestHtml(), 'body');
        $this->assertSame($document->querySelector('body'), $assertable->root);

        $assertable->with('ul.inner', function (AssertableHtml $assertable) use ($document): void {
            $this->assertSame($document->querySelector('body ul.inner'), $assertable->root);

            $assertable->with('li:first-of-type', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document->querySelector('body ul.inner li:first-of-type'), $assertable->root);
            });

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable) use ($document): void {
                $this->assertSame($document->querySelector('ul.outer'), $assertable->root);
            });
        });
    }

    public function test_get_selector(): void
    {
        $assertable = new AssertableHtml($this->getTestHtml(), 'body');
        $this->assertSame('body', $assertable->selector);

        $assertable->with('ul.inner', function (AssertableHtml $assertable): void {
            $this->assertSame('body ul.inner', $assertable->selector);

            $assertable->with('li:first-of-type', function (AssertableHtml $assertable): void {
                $this->assertSame('body ul.inner li:first-of-type', $assertable->selector);
            });

            $assertable->elsewhere('ul.outer', function (AssertableHtml $assertable): void {
                $this->assertSame('ul.outer', $assertable->selector);
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
