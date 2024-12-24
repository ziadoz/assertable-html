<?php

namespace Ziadoz\AssertableHtml\Tests;

use Dom\HTMLElement;
use PHPUnit\Framework\ExpectationFailedException;
use Ziadoz\AssertableHtml\Elements\AssertableElement;

class AssertableElementTest extends TestCase
{
    public function test_determine_root_matches_no_elements(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element selector [foobar] matches 0 elements instead of exactly 1 element.');

        new AssertableElement($this->getFixtureElement('<ul><li>Foo</li><li>Bar</li></ul>'), 'foobar');
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

        new AssertableElement($this->getFixtureElement('<ul><li>Foo</li><li>Bar</li></ul>'), 'li');
    }

    public function test_get_document(): void
    {
        $assertable = new AssertableElement($element = $this->getFixtureElement('<ul><li>Foo</li></ul>'), 'li');
        $this->assertSame($element->ownerDocument, $assertable->getDocument());
    }

    public function test_get_root(): void
    {
        $assertable = new AssertableElement($element = $this->getFixtureElement('<ul><li>Foo</li></ul>'), 'li');
        $this->assertSame($element->querySelector('li'), $assertable->getRoot());
    }

    public function test_get_html(): void
    {
        $assertable = new AssertableElement($this->getFixtureElement('<ul><li>Foo</li></ul>'), 'li');
        $this->assertSame('<li>Foo</li>', $assertable->getHtml());
    }

    public function test_assert_element_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertElement(fn (HTMLElement $element): bool => $element->tagName === 'LI');
    }

    public function test_assert_element_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] does not pass the given callback.');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertElement(fn (HTMLElement $element): bool => $element->tagName === 'P');
    }

    public function test_assert_matches_selector_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertMatchesSelector('li.foo');
    }

    public function test_assert_matches_selector_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] does not match the given selector [p].');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertMatchesSelector('p');
    }

    public function test_assert_doesnt_match_selector_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertDoesntMatchSelector('p');
    }

    public function test_assert_doesnt_match_selector_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] matches the given selector [li.foo].');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertDoesntMatchSelector('li.foo');
    }

    public function test_assert_text_passes(): void
    {
        $html = $this->getFixtureElement(<<<'HTML'
        <div>
            <p>
                Hello,
                <strong>World!</strong>
            </p>
        </div>
        HTML);

        new AssertableElement($html, 'p')
            ->assertText(fn (string $text): bool => $text === 'Hello, World!');

        new AssertableElement($html, 'p')
            ->assertText(fn (string $text): bool => str_contains($text, 'Hello') && str_contains($text, 'World!'), false);
    }

    public function test_assert_text_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [p] text does not pass the given callback.');

        $html = $this->getFixtureElement(<<<'HTML'
        <div>
            <p>
                Hello,
                <strong>World!</strong>
            </p>
        </div>
        HTML);

        new AssertableElement($html, 'p')
            ->assertText(fn (string $text): bool => $text !== 'Hello, World!');
    }

    public function test_assert_text_contains_passes(): void
    {
        $html = $this->getFixtureElement(<<<'HTML'
        <div>
            <p>
                Hello,
                <strong>World!</strong>
            </p>
        </div>
        HTML);

        new AssertableElement($html, 'p')->assertTextContains('Hello');
        new AssertableElement($html, 'p')->assertTextContains('World!', false);
    }

    public function test_assert_text_contains_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [p] text does not contain the given text.');

        $html = $this->getFixtureElement(<<<'HTML'
        <div>
            <p>
                Hello,
                <strong>World!</strong>
            </p>
        </div>
        HTML);

        new AssertableElement($html, 'p')->assertTextContains('Foo, Bar!');
    }

    public function test_assert_text_doesnt_contain_passes(): void
    {
        $html = $this->getFixtureElement(<<<'HTML'
        <div>
            <p>
                Hello,
                <strong>World!</strong>
            </p>
        </div>
        HTML);

        new AssertableElement($html, 'p')->assertTextDoesntContain('Foo');
        new AssertableElement($html, 'p')->assertTextDoesntContain('Bar!', false);
    }

    public function test_assert_text_doesnt_contain_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [p] text contains the given text.');

        $html = $this->getFixtureElement(<<<'HTML'
        <div>
            <p>
                Hello,
                <strong>World!</strong>
            </p>
        </div>
        HTML);

        new AssertableElement($html, 'p')->assertTextDoesntContain('Hello, World!');
    }

    public function test_assert_class_contains_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertClassContains('foo');
    }

    public function test_assert_class_contains_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] class does not match the given class [.bar]');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertClassContains('bar');
    }

    public function test_assert_class_doesnt_contain_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertClassDoesntContain('bar');
    }

    public function test_assert_class_doesnt_contain_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo] class matches the given class [.foo]');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertClassDoesntContain('foo');
    }
}
