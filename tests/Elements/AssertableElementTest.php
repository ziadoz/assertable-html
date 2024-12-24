<?php

namespace Ziadoz\AssertableHtml\Tests;

use PHPUnit\Framework\ExpectationFailedException;
use Ziadoz\AssertableHtml\Elements\AssertableElement;

class AssertableElementTest extends TestCase
{
    public function test_determine_root_matches_no_elements(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element selector [foobar] matches 0 elements instead of exactly 1 element.');

        new AssertableElement($this->getFixtureElement('<ul><li>Foo</li><li>Bar</li></ul>', 'ul'), 'foobar');
    }

    public function test_determine_root_matches_multiple_elements(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element selector [li] matches 2 elements instead of exactly 1 element.');

        new AssertableElement($this->getFixtureElement('<ul><li>Foo</li><li>Bar</li></ul>', 'ul'), 'li');
    }

    public function test_get_document(): void
    {
        $assertable = new AssertableElement($element = $this->getFixtureElement('<ul><li>Foo</li></ul>', 'ul'), 'li');
        $this->assertSame($element->ownerDocument, $assertable->getDocument());
    }

    public function test_get_root(): void
    {
        $assertable = new AssertableElement($element = $this->getFixtureElement('<ul><li>Foo</li></ul>', 'ul'), 'li');
        $this->assertSame($element->querySelector('li'), $assertable->getRoot());
    }

    public function test_get_html(): void
    {
        $assertable = new AssertableElement($this->getFixtureElement('<ul><li>Foo</li></ul>', 'ul'), 'li');
        $this->assertSame('<li>Foo</li>', $assertable->getHtml());
    }

    public function test_assert_matches_passes(): void
    {
        $this->markTestSkipped('How do you do this?');
    }

    public function test_assert_matches_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(
            'The element [li#qux.foo] does not match the given selector [p]:' . "\n\n" .
            '<li id="qux" class="foo">Foo</li>'
        );

        new AssertableElement($this->getFixtureElement('<ul><li id="qux" class="foo">Foo</li></ul>', 'ul'), 'li')->assertMatches('p');
    }
}
