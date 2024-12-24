<?php
namespace Ziadoz\AssertableHtml\Elements;

use Dom\Document;
use Dom\HtmlElement;
use PHPUnit\Framework\Assert as PHPUnit;
use Ziadoz\AssertableHtml\Utilities;

class AssertableElement implements AssertableElementInterface
{
    /** The root element to perform assertions on.*/
    protected HtmlElement $root;

    /** The selector that was used to select the HTML element. */
    protected string $selector;

    /** Create an assertable HTML element. */
    public function __construct(HtmlElement $element, string $selector)
    {
        $this->root = $this->determineRoot($element, $selector);
        $this->selector = $selector;
    }

    /** Determine the root element to perform assertions on. The root can only ever be a single element. */
    protected function determineRoot(HtmlElement $element, string $selector): HtmlElement
    {
        $nodes = $element->querySelectorAll($selector);

        PHPUnit::assertCount(
            1,
            $nodes,
            sprintf(
                "The element selector [%s] matches %d elements instead of exactly 1 element.\n\n%s",
                $selector,
                count($nodes),
                trim(implode(
                    "\n", array_map(
                        fn (HtmlElement $node): string => $element->ownerDocument->saveHtml($node),
                        iterator_to_array($nodes->getIterator(),
                    ),
                ))),
            ),
        );

        return $nodes[0];
    }

    /** Return the underlying HTML document instance. */
    public function getDocument(): Document
    {
        return $this->root->ownerDocument;
    }

    /** Return the root HTML element assertions are being performed on. */
    public function getRoot(): HtmlElement
    {
        return $this->root;
    }

    /** Return the root element HTML. */
    public function getHtml(): string
    {
        return $this->root->ownerDocument->saveHtml($this->root);
    }

    /** Dump the root element HTML. */
    public function dump(): void
    {
        dump($this->getHtml());
    }

    /** Dump and die the root element HTML. */
    public function dd(): never
    {
        dd($this->getHtml());
    }

    /** Assert the element matches the given selector. */
    public function assertMatches(string $selector): void
    {
        PHPUnit::assertTrue(
            $this->root->matches($selector),
            sprintf(
                "The element [%s] does not match the given selector [%s]:\n\n%s",
                Utilities::selectorFromElement($this->root),
                $selector,
                $this->getHtml(),
            ),
        );
    }
}
