<?php

namespace Ziadoz\AssertableHtml\Tests\Matchers;

use PHPUnit\Framework\Attributes\DataProvider;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Elements\AssertableFormElement;
use Ziadoz\AssertableHtml\Matchers\AssertableElementMatcher;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableElementMatcherTest extends TestCase
{
    #[DataProvider('match_data_provider')]
    public function test_match(string $html, string $assertable): void
    {
        $this->assertInstanceOf(
            $assertable,
            (new AssertableElementMatcher)->match($html = $this->getTestElement($html)),
        );
    }

    public static function match_data_provider(): iterable
    {
        yield 'form' => ['<form></form>', AssertableFormElement::class];
        yield 'fallback' => ['<p></p>', AssertableElement::class];
    }
}
