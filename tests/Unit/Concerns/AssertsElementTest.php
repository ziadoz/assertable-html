<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Concerns;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableAttributesList;
use Ziadoz\AssertableHtml\Dom\AssertableClassesList;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableText;

class AssertsElementTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Assert Exists
    |--------------------------------------------------------------------------
    */

    public function test_assert_elements_exists_passes(): void
    {
        $this->getAssertableElement('<div><p>Foo</p></div>')
            ->assertElementsExist('p');
    }

    public function test_assert_elements_exists_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [div] doesn't contain any elements matching the given selector [foo].");

        $this->getAssertableElement('<div><p>Foo</p></div>')
            ->assertElementsExist('foo');
    }

    public function test_assert_elements_dont_exist_passes(): void
    {
        $this->getAssertableElement('<div><p>Foo</p></div>')
            ->assertElementsDontExist('foo');
    }

    public function test_assert_elements_dont_exist_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [div] contains elements matching the given selector [p].');

        $this->getAssertableElement('<div><p>Foo</p></div>')
            ->assertElementsDontExist('p');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Tag
    |--------------------------------------------------------------------------
    */

    public function test_assert_tag_passes(): void
    {
        $this->getAssertableElement('<foo-bar>Foo Bar</foo-bar>')
            ->assertTagEquals('foo-bar');
    }

    public function test_assert_tag_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [foo-bar] tag doesn't match the given tag [p].");

        $this->getAssertableElement('<foo-bar>Foo Bar</foo-bar>')
            ->assertTagEquals('p');
    }

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

    public function test_assert_count_comparisons_pass(): void
    {
        $assertable = $this->getAssertableElement(<<<'HTML'
        <ul>
            <li>Foo</li>
            <li>Bar</li>
            <li>Baz</li>
            <li>Qux</li>
        </ul>
        HTML);

        $assertable->assertElementsCount('li', 4);
        $assertable->assertElementsCountGreaterThan('li', 1);
        $assertable->assertElementsCountGreaterThanOrEqual('li', 4);
        $assertable->assertElementsCountLessThan('li', 5);
        $assertable->assertElementsCountLessThanOrEqual('li', 4);
    }

    #[DataProvider('assert_count_comparisons_fail_data_provider')]
    public function test_assert_count_comparisons_fail(string $selector, string $comparison, int $expected, string $message): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage($message);

        $assertable = $this->getAssertableElement(<<<'HTML'
        <ul>
            <li>Foo</li>
            <li>Bar</li>
            <li>Baz</li>
            <li>Qux</li>
        </ul>
        HTML);

        match ($comparison) {
            '='  => $assertable->assertElementsCount($selector, $expected),
            '>'  => $assertable->assertElementsCountGreaterThan($selector, $expected),
            '>=' => $assertable->assertElementsCountGreaterThanOrEqual($selector, $expected),
            '<'  => $assertable->assertElementsCountLessThan($selector, $expected),
            '<=' => $assertable->assertElementsCountLessThanOrEqual($selector, $expected),
        };
    }

    public static function assert_count_comparisons_fail_data_provider(): iterable
    {
        yield 'equals' => [
            'li', '=', 1, "The element [ul] doesn't have exactly [1] elements matching the given selector [li].",
        ];

        yield 'greater than' => [
            'li', '>', 5, "The element [ul] doesn't have greater than [5] elements matching the given selector [li].",
        ];

        yield 'greater than or equal to' => [
            'li', '>=', 5, "The element [ul] doesn't have greater than or equal to [5] elements matching the given selector [li].",
        ];

        yield 'less than' => [
            'li', '<', 1, "The element [ul] doesn't have less than [1] elements matching the given selector [li].",
        ];

        yield 'less than or equal to' => [
            'li', '<=', 1, "The element [ul] doesn't have less than or equal to [1] elements matching the given selector [li].",
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

    /*
    |--------------------------------------------------------------------------
    | Assert ID Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_id_equals_passes(): void
    {
        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertIdEquals('foo');
    }

    public function test_assert_id_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo] id doesn't equal the given value [bar].");

        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertIdEquals('bar');
    }

    public function test_assert_id_doesnt_equal_passes(): void
    {
        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertIdDoesntEqual('bar');
    }

    public function test_assert_id_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo] id equals the given value [foo].');

        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertIdDoesntEqual('foo');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_passes(): void
    {
        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClass(function (AssertableClassesList $classes): bool {
                return $classes->contains('foo');
            });
    }

    public function test_assert_class_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't pass the given callback.");

        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClass(function (AssertableClassesList $classes): bool {
                return $classes->contains('baz');
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Classes Empty
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_empty_passes(): void
    {
        $this->getAssertableElement('<p class="">Foo</p>')
            ->assertClassEmpty();
    }

    public function test_assert_class_empty_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo] class list isn't empty.");

        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertClassEmpty();
    }

    public function test_assert_class_not_empty_passes(): void
    {
        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertClassNotEmpty();
    }

    public function test_assert_class_not_empty_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] class list is empty.');

        $this->getAssertableElement('<p class="">Foo</p>')
            ->assertClassNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Present/Missing
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_present_passes(): void
    {
        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertClassPresent();
    }

    public function test_assert_class_present_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] is missing the class attribute.');

        $this->getAssertableElement('<p>Foo</p>')
            ->assertClassPresent();
    }

    public function test_assert_class_missing_passes(): void
    {
        $this->getAssertableElement('<p>Foo</p>')
            ->assertClassMissing();
    }

    public function test_assert_class_missing_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo] has the class attribute.');

        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertClassMissing();
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_equals_passes(): void
    {
        $this->getAssertableElement('<p class="  foo bar  ">Foo</p>')
            ->assertClassEquals('foo bar', true);

        $this->getAssertableElement('<p class="  foo  bar  ">Foo</p>')
            ->assertClassEquals('  foo  bar  ');
    }

    public function test_assert_class_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't equal the given class [baz qux].");

        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassEquals('baz qux');
    }

    public function test_assert_class_doesnt_equal_passes(): void
    {
        $this->getAssertableElement('<p class="  foo bar  ">Foo</p>')
            ->assertClassDoesntEqual('baz qux', true);

        $this->getAssertableElement('<p class="  foo  bar  ">Foo</p>')
            ->assertClassDoesntEqual('  baz  qux  ');
    }

    public function test_assert_class_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo.bar] class equals the given class [foo bar].');

        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassDoesntEqual('foo bar');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_contains_passes(): void
    {
        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassContains('foo');
    }

    public function test_assert_class_contains_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar] class doesn't contain the given class [baz].");

        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassContains('baz');
    }

    public function test_assert_class_doesnt_contain_passes(): void
    {
        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassDoesntContain('baz');
    }

    public function test_assert_class_doesnt_contain_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo.bar] class contains the given class [foo].');

        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassDoesntContain('foo');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Class Contains Any / All
    |--------------------------------------------------------------------------
    */

    public function test_assert_class_contains_any_passes(): void
    {
        $this->getAssertableElement('<p class="foo bar baz">Foo</p>')
            ->assertClassContainsAny(['foo', 'bar']);
    }

    public function test_assert_class_contains_any_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar.baz] class doesn't contain any of the given classes [qux nux].");

        $this->getAssertableElement('<p class="foo bar baz">Foo</p>')
            ->assertClassContainsAny(['qux', 'nux']);
    }

    public function test_assert_class_doesnt_contain_any_passes(): void
    {
        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassDoesntContainAny(['baz', 'qux']);
    }

    public function test_assert_class_doesnt_contain_any_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo.bar] class contains some of the given classes [foo bar].');

        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassDoesntContainAny(['foo', 'bar']);
    }

    public function test_assert_class_contains_all_passes(): void
    {
        $this->getAssertableElement('<p class="foo bar baz">Foo</p>')
            ->assertClassContainsAll(['foo', 'bar']);
    }

    public function test_assert_class_contains_all_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo.bar.baz] class doesn't contain all the given classes [foo qux].");

        $this->getAssertableElement('<p class="foo bar baz">Foo</p>')
            ->assertClassContainsAll(['foo', 'qux']);
    }

    public function test_assert_class_doesnt_contain_all_passes(): void
    {
        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassDoesntContainAll(['foo', 'bar', 'baz']);
    }

    public function test_assert_class_doesnt_contain_all_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p.foo.bar] class contains all the given classes [foo bar].');

        $this->getAssertableElement('<p class="foo bar">Foo</p>')
            ->assertClassDoesntContainAll(['foo', 'bar']);
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute
    |--------------------------------------------------------------------------
    */

    public function test_assert_attributes_passes(): void
    {
        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertAttributes(function (AssertableAttributesList $attributes): bool {
                return $attributes['id'] === 'foo';
            });
    }

    public function test_assert_attributes_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo] attributes don't pass the given callback.");

        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertAttributes(function (AssertableAttributesList $attributes): bool {
                return $attributes['id'] === 'bar';
            });
    }

    public function test_assert_attribute_passes(): void
    {
        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertAttribute('id', fn (?string $value): bool => $value === 'foo');
    }

    public function test_assert_attribute_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo] attribute [id] doesn't pass the given callback.");

        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertAttribute('id', fn (?string $value): bool => $value === 'bar');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Array
    |--------------------------------------------------------------------------
    */

    public function test_assert_attributes_equal_array_passes(): void
    {
        $this->getAssertableElement('<p id="foo" data-bar="  foo-bar  " aria-label="foo">Foo</p>')
            ->assertAttributesEqualArray([
                'id'         => 'foo',
                'data-bar'   => '  foo-bar  ',
                'aria-label' => 'foo',
            ])->assertAttributesEqualArray([
                'id'         => 'foo',
                'data-bar'   => 'foo-bar',
                'aria-label' => 'foo',
            ], true);
    }

    public function test_assert_attributes_equal_array_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo] attributes don't equal the given array.");

        $this->getAssertableElement('<p id="foo" data-bar="  foo-bar  " aria-label="foo">Foo</p>')
            ->assertAttributesEqualArray([
                'id'         => 'baz',
                'data-baz'   => 'qux-nux',
                'aria-label' => 'foo-bar',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attributes Empty
    |--------------------------------------------------------------------------
    */

    public function test_assert_attributes_empty_passes(): void
    {
        $this->getAssertableElement('<p>Foo</p>')
            ->assertAttributesEmpty();
    }

    public function test_assert_attributes_empty_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p.foo] attribute list isn't empty.");

        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertAttributesEmpty();
    }

    public function test_assert_attributes_not_empty_passes(): void
    {
        $this->getAssertableElement('<p class="foo">Foo</p>')
            ->assertAttributesNotEmpty();
    }

    public function test_assert_attributes_not_empty_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p] attribute list is empty.');

        $this->getAssertableElement('<p>Foo</p>')
            ->assertAttributesNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Present/Missing
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_present_passes(): void
    {
        $this->getAssertableElement('<p id="foo">Hello, <strong>World!</strong></p>')
            ->assertAttributePresent('id');
    }

    public function test_assert_attribute_present_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo] is missing the given attribute [foo].');

        $this->getAssertableElement('<p id="foo">Hello, <strong>World!</strong></p>')
            ->assertAttributePresent('foo');
    }

    public function test_assert_attribute_missing_passes(): void
    {
        $this->getAssertableElement('<p id="foo">Hello, <strong>World!</strong></p>')
            ->assertAttributeMissing('foo');
    }

    public function test_assert_attribute_missing_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo] has the given attribute [id].');

        $this->getAssertableElement('<p id="foo">Hello, <strong>World!</strong></p>')
            ->assertAttributeMissing('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Equals
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_equals_passes(): void
    {
        $this->getAssertableElement('<p id="  foo  ">Foo</p>')
            ->assertAttributeEquals('id', 'foo', true);

        $this->getAssertableElement('<p id="  foo  ">Foo</p>')
            ->assertAttributeEquals('id', '  foo  ');
    }

    public function test_assert_attribute_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo] attribute [id] doesn't equal the given value [bar].");

        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertAttributeEquals('id', 'bar');
    }

    public function test_assert_attribute_doesnt_equal_passes(): void
    {
        $this->getAssertableElement('<p id="  foo  ">Foo</p>')
            ->assertAttributeDoesntEqual('id', 'bar', true);

        $this->getAssertableElement('<p id="  foo  ">Foo</p>')
            ->assertAttributeDoesntEqual('id', '  bar  ');
    }

    public function test_assert_attribute_doesnt_equal_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo] attribute [id] equals the given value [foo].');

        $this->getAssertableElement('<p id="foo">Foo</p>')
            ->assertAttributeDoesntEqual('id', 'foo');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Contains
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_contains_passes(): void
    {
        $this->getAssertableElement('<p id="  foo-bar-baz  ">Foo</p>')
            ->assertAttributeContains('id', 'foo-bar-', true);

        $this->getAssertableElement('<p id="  foo-bar-baz  ">Foo</p>')
            ->assertAttributeContains('id', '  foo-bar-');
    }

    public function test_assert_attribute_contains_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo-bar-baz] attribute [id] doesn't contain the given value [-qux-]");

        $this->getAssertableElement('<p id="foo-bar-baz">Foo</p>')
            ->assertAttributeContains('id', '-qux-');
    }

    public function test_assert_attribute_doesnt_contain_passes(): void
    {
        $this->getAssertableElement('<p id="  foo-bar-baz  ">Foo</p>')
            ->assertAttributeDoesntContain('id', '-qux-', true);

        $this->getAssertableElement('<p id="  foo-bar-baz  ">Foo</p>')
            ->assertAttributeDoesntContain('id', '-qux-');
    }

    public function test_assert_attribute_doesnt_contain_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo-bar-baz] attribute [id] contains the given value [-bar-].');

        $this->getAssertableElement('<p id="foo-bar-baz">Foo</p>')
            ->assertAttributeDoesntContain('id', '-bar-');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Starts With
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_starts_with_passes(): void
    {
        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeStartsWith('id', 'foo-', true);

        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeStartsWith('id', '  foo-');
    }

    public function test_assert_attribute_starts_with_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo-bar] attribute [id] doesn't start with the given prefix [baz-]");

        $this->getAssertableElement('<p id="foo-bar">')
            ->assertAttributeStartsWith('id', 'baz-');
    }

    public function test_assert_attribute_doesnt_start_with_passes(): void
    {
        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeDoesntStartWith('id', 'baz-', true);

        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeDoesntStartWith('id', '  baz-');
    }

    public function test_assert_attribute_doesnt_start_with_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo-bar] attribute [id] starts with the given prefix [foo-]');

        $this->getAssertableElement('<p id="foo-bar">')
            ->assertAttributeDoesntStartWith('id', 'foo-');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Attribute Ends With
    |--------------------------------------------------------------------------
    */

    public function test_assert_attribute_ends_with_passes(): void
    {
        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeEndsWith('id', '-bar', true);

        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeEndsWith('id', '-bar  ');
    }

    public function test_assert_attribute_ends_with_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [p#foo-bar] attribute [id] doesn't end with the given suffix [-baz]");

        $this->getAssertableElement('<p id="foo-bar">')
            ->assertAttributeEndsWith('id', '-baz');
    }

    public function test_assert_attribute_doesnt_end_with_passes(): void
    {
        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeDoesntEndWith('id', '-baz', true);

        $this->getAssertableElement('<p id="  foo-bar  ">')
            ->assertAttributeDoesntEndWith('id', '-baz  ');
    }

    public function test_assert_attribute_doesnt_end_with_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element [p#foo-bar] attribute [id] ends with the given suffix [-bar]');

        $this->getAssertableElement('<p id="foo-bar">')
            ->assertAttributeDoesntEndWith('id', '-bar');
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Data Attribute
    |--------------------------------------------------------------------------
    */

    public function test_assert_data_attribute_aliases_pass(): void
    {
        $assertable = $this->getAssertableElement('<p data-foo="foo-bar">Foo</p>');
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
        $assertable = $this->getAssertableElement('<p aria-foo="foo-bar">Foo</p>');
        $assertable->assertAriaAttribute('foo', fn (?string $value) => $value === 'foo-bar');
        $assertable->assertAriaAttributeEquals('foo', 'foo-bar');
        $assertable->assertAriaAttributeDoesntEqual('foo', 'baz-qux');
        $assertable->assertAriaAttributeContains('foo', 'bar');
        $assertable->assertAriaAttributeDoesntContain('foo', 'qux');
    }

    /*
    |--------------------------------------------------------------------------
    | Test Helper
    |--------------------------------------------------------------------------
    */

    private function getAssertableElement(string $html): AssertableElement
    {
        return AssertableDocument::createFromString($html, LIBXML_HTML_NOIMPLIED)->querySelector('*:first-of-type');
    }
}
