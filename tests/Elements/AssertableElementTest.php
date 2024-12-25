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

    /*
    |--------------------------------------------------------------------------
    | Assert Element
    |--------------------------------------------------------------------------
    */

    public function test_assert_element_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertElement(fn (HTMLElement $element): bool => $element->tagName === 'LI');
    }

    public function test_assert_element_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The element [li.foo] doesn't pass the given callback.");

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertElement(fn (HTMLElement $element): bool => $element->tagName === 'P');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Matches Selector
    |--------------------------------------------------------------------------
    */

    public function test_assert_matches_selector_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertMatchesSelector('li.foo');
    }

    public function test_assert_matches_selector_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The element [li.foo] doesn't match the given selector [p].");

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

    /*
    |--------------------------------------------------------------------------
    | Assert Text
    |--------------------------------------------------------------------------
    */

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
        $this->expectExceptionMessage("The element [p] text doesn't pass the given callback.");

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

    /*
    |--------------------------------------------------------------------------
    | Assert Text Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_text_equals_passes(): void
    {
        $html = $this->getFixtureElement('<div><p>Hello, <strong>World!</strong></p></div>');

        new AssertableElement($html, 'p')->assertTextEquals('Hello, World!');
        new AssertableElement($html, 'p')->assertTextEquals('Hello, World!', false);
    }

    public function test_assert_text_equals_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The element [p] text doesn't equal the given text.");

        new AssertableElement($this->getFixtureElement('<div><p>Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertTextEquals('Foo, Bar!');
    }

    public function test_assert_text_doesnt_equal_passes(): void
    {
        $html = $this->getFixtureElement('<div><p>Hello, <strong>World!</strong></p></div>');

        new AssertableElement($html, 'p')->assertTextDoesntEqual('Foo, Bar!');
        new AssertableElement($html, 'p')->assertTextDoesntEqual('Foo, Bar!', false);
    }

    public function test_assert_text_doesnt_equal_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [p] text equals the given text.');

        new AssertableElement($this->getFixtureElement('<div><p>Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertTextDoesntEqual('Hello, World!');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Contains
    |--------------------------------------------------------------------------
    */

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
        new AssertableElement($html, 'p')->assertTextContains('World', false);
    }

    public function test_assert_text_contains_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The element [p] text doesn't contain the given text.");

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
        new AssertableElement($html, 'p')->assertTextDoesntContain('Bar', false);
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

        new AssertableElement($html, 'p')->assertTextDoesntContain('Hello, World');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo bar">Foo</li></ul>'), 'li')
            ->assertClass(fn (array $classes): bool => in_array('foo', $classes));
    }

    public function test_assert_class_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The element [li.foo.bar] class doesn't pass the given callback.");

        new AssertableElement($this->getFixtureElement('<ul><li class="foo bar">Foo</li></ul>'), 'li')
            ->assertClass(fn (array $classes): bool => in_array('baz', $classes));
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_equals_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo bar">Foo</li></ul>'), 'li')
            ->assertClassEquals('foo bar');

        new AssertableElement($this->getFixtureElement('<ul><li class="  foo  bar  ">Foo</li></ul>'), 'li')
            ->assertClassEquals('  foo  bar  ', false);
    }

    public function test_assert_class_equals_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The element [li.foo.bar] class doesn't equal the given class [baz qux]");

        new AssertableElement($this->getFixtureElement('<ul><li class="foo bar">Foo</li></ul>'), 'li')
            ->assertClassEquals('baz qux');
    }

    public function test_assert_class_doesnt_equal_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo bar">Foo</li></ul>'), 'li')
            ->assertClassDoesntEqual('baz qux');

        new AssertableElement($this->getFixtureElement('<ul><li class="  foo  bar  ">Foo</li></ul>'), 'li')
            ->assertClassDoesntEqual('  baz  qux  ', false);
    }

    public function test_assert_class_doesnt_equal_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The element [li.foo.bar] class equals the given class [foo bar]');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo bar">Foo</li></ul>'), 'li')
            ->assertClassDoesntEqual('foo bar');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_contains_passes(): void
    {
        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertClassContains('foo');
    }

    public function test_assert_class_contains_fails(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("The element [li.foo] class doesn't match the given class [bar]");

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
        $this->expectExceptionMessage('The element [li.foo] class matches the given class [foo]');

        new AssertableElement($this->getFixtureElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertClassDoesntContain('foo');
    }
}
