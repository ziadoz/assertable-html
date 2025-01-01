<?php

namespace Ziadoz\AssertableHtml\Tests\Unit\Elements;

use Dom\HTMLDocument;
use Dom\HTMLElement;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableElementTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Assert Title
    |--------------------------------------------------------------------------
    */

    public function test_assert_title_equals(): void
    {
        $html = HTMLDocument::createFromString(<<<'HTML'
            <!DOCTYPE html>
            <html>
            <head>
                <title>  Foo - Bar  </title>
            </head>
            <body>
                <p>Foo Bar</p>
            </body>
            </html>
        HTML);

        new AssertableElement($html, 'body')->assertTitleEquals('Foo - Bar');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Element
    |--------------------------------------------------------------------------
    */

    public function test_assert_element_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="foo">Foo</p></div>'), 'p')
            ->assertElement(fn (HTMLElement $element): bool => $element->tagName === 'P' && $element->classList->value === 'foo');
    }

    public function test_assert_element_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo] doesn't pass the given callback.");

        new AssertableElement($this->getTestElement('<div><p class="foo">Foo</p></div>'), 'p')
            ->assertElement(fn (HTMLElement $element): bool => $element->tagName === 'LI');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Matches Selector
    |--------------------------------------------------------------------------
    */

    public function test_assert_matches_selector_passes(): void
    {
        new AssertableElement($this->getTestElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertMatchesSelector('li.foo');
    }

    public function test_assert_matches_selector_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [li.foo] doesn't match the given selector [p.foo].");

        new AssertableElement($this->getTestElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertMatchesSelector('p.foo');
    }

    public function test_assert_doesnt_match_selector_passes(): void
    {
        new AssertableElement($this->getTestElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertDoesntMatchSelector('p');
    }

    public function test_assert_doesnt_match_selector_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [li.foo] matches the given selector [li.foo].');

        new AssertableElement($this->getTestElement('<ul><li class="foo">Foo</li></ul>'), 'li')
            ->assertDoesntMatchSelector('li.foo');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Count
    |--------------------------------------------------------------------------
    */

    public function test_assert_number_of_elements_passes(): void
    {
        $html = $this->getTestElement(<<<'HTML'
            <div>
                <ul>
                    <li>Foo</li>
                    <li>Bar</li>
                    <li>Baz</li>
                    <li>Qux</li>
                </ul>
            </div>
        HTML);

        $assertable = new AssertableElement($html, 'ul');
        $assertable->assertNumberOfElements('li', '=', 4);
        $assertable->assertNumberOfElements('li', '>', 1);
        $assertable->assertNumberOfElements('li', '>=', 4);
        $assertable->assertNumberOfElements('li', '<', 5);
        $assertable->assertNumberOfElements('li', '<=', 4);

        $assertable->assertElementsCount('li', 4);
        $assertable->assertElementsGreaterThan('li', 1);
        $assertable->assertElementsGreaterThanOrEqual('li', 4);
        $assertable->assertElementsLessThan('li', 5);
        $assertable->assertElementsLessThanOrEqual('li', 4);
    }

    #[DataProvider('assert_number_of_elements_fails_data_provider')]
    public function test_assert_number_of_elements_fails(string $selector, string $comparison, int $expected, string $message): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage($message);

        $html = $this->getTestElement(<<<'HTML'
            <div>
                <ul>
                    <li>Foo</li>
                    <li>Bar</li>
                    <li>Baz</li>
                    <li>Qux</li>
                </ul>
            </div>
        HTML);

        $assertable = new AssertableElement($html, 'ul');
        $assertable->assertNumberOfElements($selector, $comparison, $expected);
    }

    public static function assert_number_of_elements_fails_data_provider(): iterable
    {
        yield 'equals' => [
            'li', '=', 1, "The element [ul] doesn't have exactly [1] elements matching the selector [li].",
        ];

        yield 'greater than' => [
            'li', '>', 5, "The element [ul] doesn't have greater than [5] elements matching the selector [li].",
        ];

        yield 'greater than or equal to' => [
            'li', '>=', 5, "The element [ul] doesn't have greater than or equal to [5] elements matching the selector [li].",
        ];

        yield 'less than' => [
            'li', '<', 1, "The element [ul] doesn't have less than [1] elements matching the selector [li].",
        ];

        yield 'less than or equal to' => [
            'li', '<=', 1, "The element [ul] doesn't have less than or equal to [1] elements matching the selector [li].",
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text
    |--------------------------------------------------------------------------
    */

    public function test_assert_text_passes(): void
    {
        $html = $this->getTestElement(<<<'HTML'
            <div>
                <p>
                    Hello,
                    <strong>World!</strong>
                </p>
            </div>
        HTML);

        new AssertableElement($html, 'p')
            ->assertText(fn (string $text): bool => str_contains($text, 'Hello') && str_contains($text, 'World!'));
    }

    public function test_assert_text_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p] text doesn't pass the given callback.");

        $html = $this->getTestElement(<<<'HTML'
            <div>
                <p>
                    Hello,
                    <strong>World!</strong>
                </p>
            </div>
        HTML);

        new AssertableElement($html, 'p')
            ->assertText(fn (string $text): bool => $text === 'Hello, World!');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_text_equals_passes(): void
    {
        $html = $this->getTestElement('<div><p>  Hello, <strong>World!</strong>  </p></div>');

        $assertable = new AssertableElement($html, 'p');
        $assertable->assertTextEquals('Hello, World!', true);
        $assertable->assertTextEquals('  Hello, World!  ');
    }

    public function test_assert_text_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p] text doesn't equal the given text.");

        new AssertableElement($this->getTestElement('<div><p>Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertTextEquals('Foo, Bar!');
    }

    public function test_assert_text_doesnt_equal_passes(): void
    {
        $html = $this->getTestElement('<div><p>  Hello, <strong>World!</strong>  </p></div>');

        $assertable = new AssertableElement($html, 'p');
        $assertable->assertTextDoesntEqual('Foo, Bar!', true);
        $assertable->assertTextDoesntEqual('Foo, Bar!');
    }

    public function test_assert_text_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] text equals the given text.');

        new AssertableElement($this->getTestElement('<div><p>Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertTextDoesntEqual('Hello, World!');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Contains
    |--------------------------------------------------------------------------
    */

    public function test_assert_text_contains_passes(): void
    {
        $html = $this->getTestElement(<<<'HTML'
            <div>
                <p>
                    Hello,
                    <strong>World!</strong>
                </p>
            </div>
        HTML);

        $assertable = new AssertableElement($html, 'p');
        $assertable->assertTextContains('Hello', true);
        $assertable->assertTextContains('World');

        $assertable->assertSeeIn('Hello', true);
        $assertable->assertSeeIn('World');
    }

    public function test_assert_text_contains_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p] text doesn't contain the given text.");

        new AssertableElement($this->getTestElement('<div><p>Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertTextContains('Foo, Bar!');
    }

    public function test_assert_text_doesnt_contain_passes(): void
    {
        $html = $this->getTestElement(<<<'HTML'
            <div>
                <p>
                    Hello,
                    <strong>World!</strong>
                </p>
            </div>
        HTML);

        $assertable = new AssertableElement($html, 'p');
        $assertable->assertTextDoesntContain('Foo', true);
        $assertable->assertTextDoesntContain('Bar');

        $assertable->assertDontSeeIn('Foo', true);
        $assertable->assertDontSeeIn('Bar');
    }

    public function test_assert_text_doesnt_contain_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] text contains the given text.');

        new AssertableElement($this->getTestElement('<div><p>Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertTextDoesntContain('Hello, World!');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClass(fn (array $classes): bool => in_array('foo', $classes));
    }

    public function test_assert_class_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't pass the given callback.");

        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClass(fn (array $classes): bool => in_array('baz', $classes));
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Present/Missing
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_present_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="foo">Foo</p></div>'), 'p')
            ->assertClassPresent();
    }

    public function test_assert_class_present_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] is missing the class attribute.');

        new AssertableElement($this->getTestElement('<div><p>Foo</p></div>'), 'p')
            ->assertClassPresent();
    }

    public function test_assert_class_missing_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p>Foo</p></div>'), 'p')
            ->assertClassMissing();
    }

    public function test_assert_class_missing_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo] has the class attribute.');

        new AssertableElement($this->getTestElement('<div><p class="foo">Foo</p></div>'), 'p')
            ->assertClassMissing();
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_equals_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="  foo bar  ">Foo</p></div>'), 'p')
            ->assertClassEquals('foo bar', true);

        new AssertableElement($this->getTestElement('<div><p class="  foo  bar  ">Foo</p></div>'), 'p')
            ->assertClassEquals('  foo  bar  ');
    }

    public function test_assert_class_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't equal the given class [baz qux].");

        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassEquals('baz qux');
    }

    public function test_assert_class_doesnt_equal_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="  foo bar  ">Foo</p></div>'), 'p')
            ->assertClassDoesntEqual('baz qux', true);

        new AssertableElement($this->getTestElement('<div><p class="  foo  bar  ">Foo</p></div>'), 'p')
            ->assertClassDoesntEqual('  baz  qux  ');
    }

    public function test_assert_class_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo.bar] class equals the given class [foo bar].');

        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassDoesntEqual('foo bar');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_contains_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassContains('foo');
    }

    public function test_assert_class_contains_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't contain the given class [baz].");

        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassContains('baz');
    }

    public function test_assert_class_doesnt_contain_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassDoesntContain('baz');
    }

    public function test_assert_class_doesnt_contain_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo.bar] class contains the given class [foo].');

        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassDoesntContain('foo');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains All
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_contains_all_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="foo bar baz">Foo</p></div>'), 'p')
            ->assertClassContainsAll(['foo', 'bar']);
    }

    public function test_assert_class_contains_all_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar.baz] class doesn't contain all the given classes [foo qux].");

        new AssertableElement($this->getTestElement('<div><p class="foo bar baz">Foo</p></div>'), 'p')
            ->assertClassContainsAll(['foo', 'qux']);
    }

    public function test_assert_class_doesnt_contain_all_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassDoesntContainAll(['foo', 'bar', 'baz']);
    }

    public function test_assert_class_doesnt_contain_all_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo.bar] class contains all the given classes [foo bar].');

        new AssertableElement($this->getTestElement('<div><p class="foo bar">Foo</p></div>'), 'p')
            ->assertClassDoesntContainAll(['foo', 'bar']);
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p id="foo">Foo</p></div>'), 'p')
            ->assertAttribute('id', fn (?string $value): bool => $value === 'foo');
    }

    public function test_assert_attribute_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo] attribute [id] doesn't pass the given callback.");

        new AssertableElement($this->getTestElement('<div><p id="foo">Foo</p></div>'), 'p')
            ->assertAttribute('id', fn (?string $value): bool => $value === 'bar');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Present/Missing
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_present_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertAttributePresent('id');
    }

    public function test_assert_attribute_present_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo] is missing the given attribute [foo].');

        new AssertableElement($this->getTestElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertAttributePresent('foo');
    }

    public function test_assert_attribute_missing_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertAttributeMissing('foo');
    }

    public function test_assert_attribute_missing_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo] has the given attribute [id].');

        new AssertableElement($this->getTestElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'), 'p')
            ->assertAttributeMissing('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_equals_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p id="  foo  ">Foo</p></div>'), 'p')
            ->assertAttributeEquals('id', 'foo', true);

        new AssertableElement($this->getTestElement('<div><p id="  foo  ">Foo</p></div>'), 'p')
            ->assertAttributeEquals('id', '  foo  ');
    }

    public function test_assert_attribute_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo] attribute [id] doesn't equal the given value [bar].");

        new AssertableElement($this->getTestElement('<div><p id="foo">Foo</p></div>'), 'p')
            ->assertAttributeEquals('id', 'bar');
    }

    public function test_assert_attribute_doesnt_equal_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p id="  foo  ">Foo</p></div>'), 'p')
            ->assertAttributeDoesntEqual('id', 'bar', true);

        new AssertableElement($this->getTestElement('<div><p id="  foo  ">Foo</p></div>'), 'p')
            ->assertAttributeDoesntEqual('id', '  bar  ');
    }

    public function test_assert_attribute_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo] attribute [id] equals the given value [foo].');

        new AssertableElement($this->getTestElement('<div><p id="foo">Foo</p></div>'), 'p')
            ->assertAttributeDoesntEqual('id', 'foo');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Contains
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_contains_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'), 'p')
            ->assertAttributeContains('id', 'foo-bar-', true);

        new AssertableElement($this->getTestElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'), 'p')
            ->assertAttributeContains('id', '  foo-bar-');
    }

    public function test_assert_attribute_contains_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo-bar-baz] attribute [id] doesn't contain the given value [-qux-]");

        new AssertableElement($this->getTestElement('<div><p id="foo-bar-baz">Foo</p></div>'), 'p')
            ->assertAttributeContains('id', '-qux-');
    }

    public function test_assert_attribute_doesnt_contain_passes(): void
    {
        new AssertableElement($this->getTestElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'), 'p')
            ->assertAttributeDoesntContain('id', '-qux-', true);

        new AssertableElement($this->getTestElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'), 'p')
            ->assertAttributeDoesntContain('id', '-qux-');
    }

    public function test_assert_attribute_doesnt_contain_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo-bar-baz] attribute [id] contains the given value [-bar-].');

        new AssertableElement($this->getTestElement('<div><p id="foo-bar-baz">Foo</p></div>'), 'p')
            ->assertAttributeDoesntContain('id', '-bar-');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Data Attribute
    |--------------------------------------------------------------------------
    */

    public function test_assert_data_attribute_aliases_pass(): void
    {
        $assertable = new AssertableElement($this->getTestElement('<div><p data-foo="foo-bar">Foo</p></div>'), 'p');
        $assertable->assertDataAttribute('foo', fn (?string $value) => $value === 'foo-bar');
        $assertable->assertDataAttributeEquals('foo', 'foo-bar');
        $assertable->assertDataAttributeDoesntEqual('foo', 'baz-qux');
        $assertable->assertDataAttributeContains('foo', 'bar');
        $assertable->assertDataAttributeDoesntContain('foo', 'qux');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Aria Attribute
    |--------------------------------------------------------------------------
    */

    public function test_assert_aria_attribute_aliases_pass(): void
    {
        $assertable = new AssertableElement($this->getTestElement('<div><p aria-foo="foo-bar">Foo</p></div>'), 'p');
        $assertable->assertAriaAttribute('foo', fn (?string $value) => $value === 'foo-bar');
        $assertable->assertAriaAttributeEquals('foo', 'foo-bar');
        $assertable->assertAriaAttributeDoesntEqual('foo', 'baz-qux');
        $assertable->assertAriaAttributeContains('foo', 'bar');
        $assertable->assertAriaAttributeDoesntContain('foo', 'qux');
    }
}
