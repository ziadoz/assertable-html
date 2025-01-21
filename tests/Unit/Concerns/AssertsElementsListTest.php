<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Concerns;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;
use Ziadoz\AssertableHtml\Dom\AssertableElementsList;

class AssertsElementsListTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Assert Elements
    |--------------------------------------------------------------------------
    */

    public function test_assert_elements_passes(): void
    {
        AssertableDocument::createFromString('<p>Foo</p><p>Bar</p>', LIBXML_NOERROR)
            ->querySelectorAll('p')
            ->assertElements(function (AssertableElementsList $els): bool {
                return count($els) === 2;
            });
    }

    public function test_assert_elements_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element list doesn't pass the given callback.");

        AssertableDocument::createFromString('<p>Foo</p><p>Bar</p>', LIBXML_NOERROR)
            ->querySelectorAll('p')
            ->assertElements(function (AssertableElementsList $els): bool {
                return count($els) === 0;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Empty
    |--------------------------------------------------------------------------
    */

    public function test_assert_empty_passes(): void
    {
        AssertableDocument::createFromString('<ul></ul>', LIBXML_NOERROR)
            ->querySelectorAll('li')
            ->assertEmpty();
    }

    public function test_assert_empty_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element list isn't empty.");

        AssertableDocument::createFromString('<ul><li>Foo</li><li>Bar</li></ul>', LIBXML_NOERROR)
            ->querySelectorAll('li')
            ->assertEmpty();
    }

    public function test_assert_not_empty_passes(): void
    {
        AssertableDocument::createFromString('<ul><li>Foo</li><li>Bar</li></ul>', LIBXML_NOERROR)
            ->querySelectorAll('li')
            ->assertNotEmpty();
    }

    public function test_assert_not_empty_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('The element list is empty.');

        AssertableDocument::createFromString('<ul></ul>', LIBXML_NOERROR)
            ->querySelectorAll('li')
            ->assertNotEmpty();
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Count
    |--------------------------------------------------------------------------
    */

    public function test_assert_number_of_elements_passes(): void
    {
        $assertable = AssertableDocument::createFromString(<<<'HTML'
        <ul>
            <li>Foo</li>
            <li>Bar</li>
            <li>Baz</li>
            <li>Qux</li>
        </ul>
        HTML, LIBXML_NOERROR)->querySelectorAll('li');

        $assertable->assertNumberOfElements('=', 4);
        $assertable->assertNumberOfElements('>', 1);
        $assertable->assertNumberOfElements('>=', 4);
        $assertable->assertNumberOfElements('<', 5);
        $assertable->assertNumberOfElements('<=', 4);

        $assertable->assertCount(4);
        $assertable->assertCountGreaterThan(1);
        $assertable->assertCountGreaterThanOrEqual(4);
        $assertable->assertCountLessThan(5);
        $assertable->assertCountLessThanOrEqual(4);
    }

    #[DataProvider('assert_number_of_elements_fails_data_provider')]
    public function test_assert_number_of_elements_fails(string $comparison, int $expected, string $message): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage($message);

        AssertableDocument::createFromString(<<<'HTML'
        <ul>
            <li>Foo</li>
            <li>Bar</li>
            <li>Baz</li>
            <li>Qux</li>
        </ul>
        HTML, LIBXML_NOERROR)
            ->querySelectorAll('li')
            ->assertNumberOfElements($comparison, $expected);
    }

    public static function assert_number_of_elements_fails_data_provider(): iterable
    {
        yield 'equals' => [
            '=', 1, "The element list doesn't have exactly [1] elements.",
        ];

        yield 'greater than' => [
            '>', 5, "The element list doesn't have greater than [5] elements.",
        ];

        yield 'greater than or equal to' => [
            '>=', 5, "The element list doesn't have greater than or equal to [5] elements.",
        ];

        yield 'less than' => [
            '<', 1, "The element list doesn't have less than [1] elements.",
        ];

        yield 'less than or equal to' => [
            '<=', 1, "The element list doesn't have less than or equal to [1] elements.",
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Any / All
    |--------------------------------------------------------------------------
    */

    public function test_assert_any_passes(): void
    {
        AssertableDocument::createFromString('<p class="foo">Foo</p><p class="bar">Bar</p>', LIBXML_NOERROR)
            ->querySelectorAll('p')
            ->assertAny(function (AssertableElement $el): bool {
                return $el->matches('.foo');
            });
    }

    public function test_assert_any_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('No elements in the list match the given callback.');

        AssertableDocument::createFromString('<p class="foo">Foo</p><p class="bar">Bar</p>', LIBXML_NOERROR)
            ->querySelectorAll('p')
            ->assertAny(function (AssertableElement $el): bool {
                return $el->matches('.baz');
            });
    }

    public function test_assert_all_passes(): void
    {
        AssertableDocument::createFromString('<p class="foo">Foo</p><p class="foo">Bar</p>', LIBXML_NOERROR)
            ->querySelectorAll('p')
            ->assertAll(function (AssertableElement $el): bool {
                return $el->matches('.foo');
            });
    }

    public function test_assert_all_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Not every element in the list matches the given callback.');

        AssertableDocument::createFromString('<p class="foo">Foo</p><p class="bar">Bar</p>', LIBXML_NOERROR)
            ->querySelectorAll('p')
            ->assertAll(function (AssertableElement $el): bool {
                return $el->matches('.foo');
            });
    }
}
