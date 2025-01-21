<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Concerns;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableText;

class AssertsElementTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Assert Element
    |--------------------------------------------------------------------------
    */

    public function test_assert_element_passes(): void
    {
        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertElement(function (AssertableElement $element): bool {
                return $element->tag === 'p' && $element->classes->contains('foo');
            });
    }

    public function test_assert_element_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo] doesn't pass the given callback.");

        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertElement(function (AssertableElement $element): bool {
                return $element->tag === 'li';
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Matches Selector
    |--------------------------------------------------------------------------
    */

    public function test_assert_matches_selector_passes(): void
    {
        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertMatchesSelector('p.foo');
    }

    public function test_assert_matches_selector_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo] doesn't match the given selector [p.bar].");

        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertMatchesSelector('p.bar');
    }

    public function test_assert_doesnt_match_selector_passes(): void
    {
        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertDoesntMatchSelector('foo');
    }

    public function test_assert_doesnt_match_selector_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo] matches the given selector [p.foo].');

        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertDoesntMatchSelector('p.foo');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Count
    |--------------------------------------------------------------------------
    */

    public function test_assert_number_of_elements_passes(): void
    {
        $assertable = $this->getAssertableElement(<<<'HTML'
        <ul>
            <li>Foo</li>
            <li>Bar</li>
            <li>Baz</li>
            <li>Qux</li>
        </ul>
        HTML);

        $assertable->assertNumberOfElements('li', '=', 4);
        $assertable->assertNumberOfElements('li', '>', 1);
        $assertable->assertNumberOfElements('li', '>=', 4);
        $assertable->assertNumberOfElements('li', '<', 5);
        $assertable->assertNumberOfElements('li', '<=', 4);

        $assertable->assertElementsCount('li', 4);
        $assertable->assertElementsCountGreaterThan('li', 1);
        $assertable->assertElementsCountGreaterThanOrEqual('li', 4);
        $assertable->assertElementsCountLessThan('li', 5);
        $assertable->assertElementsCountLessThanOrEqual('li', 4);
    }

    #[DataProvider('assert_number_of_elements_fails_data_provider')]
    public function test_assert_number_of_elements_fails(string $selector, string $comparison, int $expected, string $message): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage($message);

        $this->getAssertableElement(<<<'HTML'
        <ul>
            <li>Foo</li>
            <li>Bar</li>
            <li>Baz</li>
            <li>Qux</li>
        </ul>
        HTML)->assertNumberOfElements($selector, $comparison, $expected);
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
        $this->getAssertableElement(<<<'HTML'
        <p>
            Hello,
            <strong>World!</strong>
        </p>
        HTML)->assertText(function (AssertableText $text): bool {
            return str_contains($text->value(), 'Hello') && str_contains($text->value(), 'World!');
        });
    }

    public function test_assert_text_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p] text doesn't pass the given callback.");

        $html = $this->getAssertableElement(<<<'HTML'
        <p>
            Hello,
            <strong>World!</strong>
        </p>
        HTML)->assertText(function (AssertableText $text): bool {
            return $text->value(true) === 'Foo, Bar!';
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_text_equals_passes(): void
    {
        $assertable = $this->getAssertableElement('<p>  Hello, <strong>World!</strong>  </p>');

        $assertable->assertTextEquals('Hello, World!', true);
        $assertable->assertTextEquals('  Hello, World!  ');
    }

    public function test_assert_text_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p] text doesn't equal the given text.");

        $this->getAssertableElement('<p>Hello, <strong>World!</strong></p>')
            ->assertTextEquals('Foo, Bar!');
    }

    public function test_assert_text_doesnt_equal_passes(): void
    {
        $assertable = $this->getAssertableElement('<p>  Hello, <strong>World!</strong>  </p>');

        $assertable->assertTextDoesntEqual('Foo, Bar!', true);
        $assertable->assertTextDoesntEqual('Foo, Bar!');
    }

    public function test_assert_text_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] text equals the given text.');

        $this->getAssertableElement('<p>Hello, <strong>World!</strong></p>')
            ->assertTextDoesntEqual('Hello, World!');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Text Contains
    |--------------------------------------------------------------------------
    */

    public function test_assert_text_contains_passes(): void
    {
        $assertable = $this->getAssertableElement(<<<'HTML'
        <p>
            Hello,
            <strong>World!</strong>
        </p>
        HTML);

        $assertable->assertTextContains('Hello', true);
        $assertable->assertTextContains('World');

        $assertable->assertSeeIn('Hello', true);
        $assertable->assertSeeIn('World');
    }

    public function test_assert_text_contains_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p] text doesn't contain the given text.");

        $this->getAssertableElement('<p>Hello, <strong>World!</strong></p>')
            ->assertTextContains('Foo, Bar!');
    }

    public function test_assert_text_doesnt_contain_passes(): void
    {
        $assertable = $this->getAssertableElement(<<<'HTML'
        <p>
            Hello,
            <strong>World!</strong>
        </p>
        HTML);

        $assertable->assertTextDoesntContain('Foo', true);
        $assertable->assertTextDoesntContain('Bar');

        $assertable->assertDontSeeIn('Foo', true);
        $assertable->assertDontSeeIn('Bar');
    }

    public function test_assert_text_doesnt_contain_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] text contains the given text.');

        $this->getAssertableElement('<p>Hello, <strong>World!</strong></p>')
            ->assertTextDoesntContain('Hello, World!');
    }

//    /*
//    |--------------------------------------------------------------------------
//    | Assert Class
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_class_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClass(fn (array $classes): bool => in_array('foo', $classes));
//    }
//
//    public function test_assert_class_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't pass the given callback.");
//
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClass(fn (array $classes): bool => in_array('baz', $classes));
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Class Present/Missing
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_class_present_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="foo">Foo</p></div>'))
//            ->assertClassPresent();
//    }
//
//    public function test_assert_class_present_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p] is missing the class attribute.');
//
//        $this->getAssertableElement('<div><p>Foo</p></div>'))
//            ->assertClassPresent();
//    }
//
//    public function test_assert_class_missing_passes(): void
//    {
//        $this->getAssertableElement('<div><p>Foo</p></div>'))
//            ->assertClassMissing();
//    }
//
//    public function test_assert_class_missing_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p.foo] has the class attribute.');
//
//        $this->getAssertableElement('<div><p class="foo">Foo</p></div>'))
//            ->assertClassMissing();
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Class Equals
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_class_equals_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="  foo bar  ">Foo</p></div>'))
//            ->assertClassEquals('foo bar', true);
//
//        $this->getAssertableElement('<div><p class="  foo  bar  ">Foo</p></div>'))
//            ->assertClassEquals('  foo  bar  ');
//    }
//
//    public function test_assert_class_equals_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't equal the given class [baz qux].");
//
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassEquals('baz qux');
//    }
//
//    public function test_assert_class_doesnt_equal_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="  foo bar  ">Foo</p></div>'))
//            ->assertClassDoesntEqual('baz qux', true);
//
//        $this->getAssertableElement('<div><p class="  foo  bar  ">Foo</p></div>'))
//            ->assertClassDoesntEqual('  baz  qux  ');
//    }
//
//    public function test_assert_class_doesnt_equal_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p.foo.bar] class equals the given class [foo bar].');
//
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassDoesntEqual('foo bar');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Class Contains
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_class_contains_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassContains('foo');
//    }
//
//    public function test_assert_class_contains_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't contain the given class [baz].");
//
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassContains('baz');
//    }
//
//    public function test_assert_class_doesnt_contain_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassDoesntContain('baz');
//    }
//
//    public function test_assert_class_doesnt_contain_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p.foo.bar] class contains the given class [foo].');
//
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassDoesntContain('foo');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Class Contains All
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_class_contains_all_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="foo bar baz">Foo</p></div>'))
//            ->assertClassContainsAll(['foo', 'bar']);
//    }
//
//    public function test_assert_class_contains_all_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p.foo.bar.baz] class doesn't contain all the given classes [foo qux].");
//
//        $this->getAssertableElement('<div><p class="foo bar baz">Foo</p></div>'))
//            ->assertClassContainsAll(['foo', 'qux']);
//    }
//
//    public function test_assert_class_doesnt_contain_all_passes(): void
//    {
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassDoesntContainAll(['foo', 'bar', 'baz']);
//    }
//
//    public function test_assert_class_doesnt_contain_all_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p.foo.bar] class contains all the given classes [foo bar].');
//
//        $this->getAssertableElement('<div><p class="foo bar">Foo</p></div>'))
//            ->assertClassDoesntContainAll(['foo', 'bar']);
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Attribute
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_attribute_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="foo">Foo</p></div>'))
//            ->assertAttribute('id', fn (?string $value): bool => $value === 'foo');
//    }
//
//    public function test_assert_attribute_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p#foo] attribute [id] doesn't pass the given callback.");
//
//        $this->getAssertableElement('<div><p id="foo">Foo</p></div>'))
//            ->assertAttribute('id', fn (?string $value): bool => $value === 'bar');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Attribute Present/Missing
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_attribute_present_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'))
//            ->assertAttributePresent('id');
//    }
//
//    public function test_assert_attribute_present_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p#foo] is missing the given attribute [foo].');
//
//        $this->getAssertableElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'))
//            ->assertAttributePresent('foo');
//    }
//
//    public function test_assert_attribute_missing_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'))
//            ->assertAttributeMissing('foo');
//    }
//
//    public function test_assert_attribute_missing_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p#foo] has the given attribute [id].');
//
//        $this->getAssertableElement('<div><p id="foo">Hello, <strong>World!</strong></p></div>'))
//            ->assertAttributeMissing('id');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Attribute Equals
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_attribute_equals_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo  ">Foo</p></div>'))
//            ->assertAttributeEquals('id', 'foo', true);
//
//        $this->getAssertableElement('<div><p id="  foo  ">Foo</p></div>'))
//            ->assertAttributeEquals('id', '  foo  ');
//    }
//
//    public function test_assert_attribute_equals_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p#foo] attribute [id] doesn't equal the given value [bar].");
//
//        $this->getAssertableElement('<div><p id="foo">Foo</p></div>'))
//            ->assertAttributeEquals('id', 'bar');
//    }
//
//    public function test_assert_attribute_doesnt_equal_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo  ">Foo</p></div>'))
//            ->assertAttributeDoesntEqual('id', 'bar', true);
//
//        $this->getAssertableElement('<div><p id="  foo  ">Foo</p></div>'))
//            ->assertAttributeDoesntEqual('id', '  bar  ');
//    }
//
//    public function test_assert_attribute_doesnt_equal_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p#foo] attribute [id] equals the given value [foo].');
//
//        $this->getAssertableElement('<div><p id="foo">Foo</p></div>'))
//            ->assertAttributeDoesntEqual('id', 'foo');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Attribute Contains
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_attribute_contains_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'))
//            ->assertAttributeContains('id', 'foo-bar-', true);
//
//        $this->getAssertableElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'))
//            ->assertAttributeContains('id', '  foo-bar-');
//    }
//
//    public function test_assert_attribute_contains_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p#foo-bar-baz] attribute [id] doesn't contain the given value [-qux-]");
//
//        $this->getAssertableElement('<div><p id="foo-bar-baz">Foo</p></div>'))
//            ->assertAttributeContains('id', '-qux-');
//    }
//
//    public function test_assert_attribute_doesnt_contain_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'))
//            ->assertAttributeDoesntContain('id', '-qux-', true);
//
//        $this->getAssertableElement('<div><p id="  foo-bar-baz  ">Foo</p></div>'))
//            ->assertAttributeDoesntContain('id', '-qux-');
//    }
//
//    public function test_assert_attribute_doesnt_contain_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p#foo-bar-baz] attribute [id] contains the given value [-bar-].');
//
//        $this->getAssertableElement('<div><p id="foo-bar-baz">Foo</p></div>'))
//            ->assertAttributeDoesntContain('id', '-bar-');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Attribute Starts With
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_attribute_starts_with_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeStartsWith('id', 'foo-', true);
//
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeStartsWith('id', '  foo-');
//    }
//
//    public function test_assert_attribute_starts_with_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p#foo-bar] attribute [id] doesn't start with the given prefix [baz-]");
//
//        $this->getAssertableElement('<div><p id="foo-bar"></div>'))
//            ->assertAttributeStartsWith('id', 'baz-');
//    }
//
//    public function test_assert_attribute_doesnt_start_with_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeDoesntStartWith('id', 'baz-', true);
//
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeDoesntStartWith('id', '  baz-');
//    }
//
//    public function test_assert_attribute_doesnt_start_with_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p#foo-bar] attribute [id] starts with the given prefix [foo-]');
//
//        $this->getAssertableElement('<div><p id="foo-bar"></div>'))
//            ->assertAttributeDoesntStartWith('id', 'foo-');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Attribute Ends With
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_attribute_ends_with_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeEndsWith('id', '-bar', true);
//
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeEndsWith('id', '-bar  ');
//    }
//
//    public function test_assert_attribute_ends_with_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage("The element [p#foo-bar] attribute [id] doesn't end with the given suffix [-baz]");
//
//        $this->getAssertableElement('<div><p id="foo-bar"></div>'))
//            ->assertAttributeEndsWith('id', '-baz');
//    }
//
//    public function test_assert_attribute_doesnt_end_with_passes(): void
//    {
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeDoesntEndWith('id', '-baz', true);
//
//        $this->getAssertableElement('<div><p id="  foo-bar  "></div>'))
//            ->assertAttributeDoesntEndWith('id', '-baz  ');
//    }
//
//    public function test_assert_attribute_doesnt_end_with_fails(): void
//    {
//        $this->expectException(AssertionFailedError::class);
//        $this->expectExceptionMessage('The element [p#foo-bar] attribute [id] ends with the given suffix [-bar]');
//
//        $this->getAssertableElement('<div><p id="foo-bar"></div>'))
//            ->assertAttributeDoesntEndWith('id', '-bar');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Data Attribute
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_data_attribute_aliases_pass(): void
//    {
//        $assertable = $this->getAssertableElement('<div><p data-foo="foo-bar">Foo</p></div>'));
//        $assertable->assertDataAttribute('foo', fn (?string $value) => $value === 'foo-bar');
//        $assertable->assertDataAttributeEquals('foo', 'foo-bar');
//        $assertable->assertDataAttributeDoesntEqual('foo', 'baz-qux');
//        $assertable->assertDataAttributeContains('foo', 'bar');
//        $assertable->assertDataAttributeDoesntContain('foo', 'qux');
//    }
//
//    /*
//    |--------------------------------------------------------------------------
//    | Assert Aria Attribute
//    |--------------------------------------------------------------------------
//    */
//
//    public function test_assert_aria_attribute_aliases_pass(): void
//    {
//        $assertable = $this->getAssertableElement('<div><p aria-foo="foo-bar">Foo</p></div>'));
//        $assertable->assertAriaAttribute('foo', fn (?string $value) => $value === 'foo-bar');
//        $assertable->assertAriaAttributeEquals('foo', 'foo-bar');
//        $assertable->assertAriaAttributeDoesntEqual('foo', 'baz-qux');
//        $assertable->assertAriaAttributeContains('foo', 'bar');
//        $assertable->assertAriaAttributeDoesntContain('foo', 'qux');
//    }

    /*
    |--------------------------------------------------------------------------
    | Test Helper
    |--------------------------------------------------------------------------
    */

    public function getAssertableElement(string $html, string $selector = 'body *:first-of-type'): AssertableElement
    {
        return AssertableDocument::createFromString($html, LIBXML_NOERROR)->querySelector($selector);
    }
}
