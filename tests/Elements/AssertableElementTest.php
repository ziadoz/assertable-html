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
        $this->expectExceptionMessage(<<<'MSG'
        The element selector [li] matches 2 elements instead of exactly 1 element.

        2 Matching Element(s) Found
        ============================

        1. [li]:
        > <li>Foo</li>

        2. [li]:
        > <li>Bar</li>
        MSG);

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
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertMatches('li.foo');
    }

    public function test_assert_matches_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] does not match the given selector [p].');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertMatches('p');
    }

    public function test_assert_doesnt_match_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertDoesntMatch('p');
    }

    public function test_assert_doesnt_match_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] matches the given selector [li.foo].');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertDoesntMatch('li.foo');
    }

    public function test_assert_class_contains_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertClassContains('foo');
    }

    public function test_assert_class_contains_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] class does not match the given class [.bar]');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertClassContains('bar');
    }

    public function test_assert_class_doesnt_contain_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertClassDoesntContain('bar');
    }

    public function test_assert_class_doesnt_contain_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] class matches the given class [.foo]');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>', 'ul'), 'li')->assertClassDoesntContain('foo');
    }
}
