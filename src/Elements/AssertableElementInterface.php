<?php
namespace Ziadoz\AssertableHtml\Elements;

use Dom\HtmlDocument;
use Dom\HtmlElement;

interface AssertableElementInterface
{
    /** Create an assertable HTML element. */
    public function __construct(HtmlElement $element, string $selector);

    /** Return the underlying HTML document instance. */
    public function getDocument(): HtmlDocument;

    /** Return the root HTML element assertions are being performed on. */
    public function getRoot(): HtmlElement;

    /** Return the root element HTML. */
    public function getHtml(): string;

    /** Dump the element HTML. */
    public function dump(): void;

    /** Dump and die the element HTML. */
    public function dd(): never;
}
