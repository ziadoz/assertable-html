<?php
namespace Ziadoz\AssertableHtml\Tests;

use PHPUnit\Framework\ExpectationFailedException;
use Ziadoz\AssertableHtml\Elements\AssertableElement;

class AssertableElementTest extends TestCase
{
    public function testDetermineRootMatchesMultipleElements()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(
            'The element selector [li] matches 2 elements instead of exactly 1 element.' . "\n\n" .
            '<li>Foo</li>' . "\n" .
            '<li>Bar</li>'
        );

        new AssertableElement($this->getFixtureElement('<ul><li>Foo</li><li>Bar</li></ul>', 'ul'), 'li');
    }

    public function testGetDocument(): void
    {
        $assertable = new AssertableElement($element = $this->getFixtureElement('<ul><li>Foo</li></ul>', 'ul'), 'li');
        $this->assertSame($element->ownerDocument, $assertable->getDocument());
    }

    public function testGetRoot(): void
    {
        $assertable = new AssertableElement($element = $this->getFixtureElement('<ul><li>Foo</li></ul>', 'ul'), 'li');
        $this->assertSame($element->querySelector('li'), $assertable->getRoot());
    }

    public function testGetHtml(): void
    {
        $assertable = new AssertableElement($this->getFixtureElement('<ul><li>Foo</li></ul>', 'ul'), 'li');
        $this->assertSame('<li>Foo</li>', $assertable->getHtml());
    }

    public function testAssertMatchesPasses(): void
    {
        $this->markTestSkipped('How do you do this?');
    }

    public function testAssertMatchesFails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(
            'The element [li#qux.foo] does not match the given selector [p]:' . "\n\n" .
            '<li id="qux" class="foo">Foo</li>'
        );

        new AssertableElement($this->getFixtureElement('<ul><li id="qux" class="foo">Foo</li></ul>', 'ul'), 'li')->assertMatches('p');
    }
}
