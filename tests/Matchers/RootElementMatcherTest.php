<?php

namespace Ziadoz\AssertableHtml\Tests\Matchers;

use PHPUnit\Framework\ExpectationFailedException;
use Ziadoz\AssertableHtml\Matchers\RootElementMatcher;
use Ziadoz\AssertableHtml\Tests\TestCase;

class RootElementMatcherTest extends TestCase
{
    public function test_match(): void
    {
        $html = $this->getFixtureElement('<div><p>Foo</p></div>');

        $this->assertSame(
            $html->querySelector('p'),
            (new RootElementMatcher)->match($html, 'p'),
        );
    }

    public function test_match_no_elements(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('The selector [foobar] matches 0 elements instead of exactly 1 element.');

        (new RootElementMatcher)->match($this->getFixtureElement('</div><p>Foo</p></div>'), 'foobar');
    }

    public function test_determine_match_multiple_elements(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(<<<'MSG'
        The selector [p] matches 4 elements instead of exactly 1 element.

        4 Matching Element(s) Found
        ============================

        1. [p]:
        > <p>Foo</p>

        2. [p]:
        > <p>Bar</p>

        3. [p]:
        > <p>Baz</p>
        MSG);

        (new RootElementMatcher)->match($this->getFixtureElement('<div><p>Foo</p><p>Bar</p><p>Baz</p><p>Qux</p></div>'), 'p');
    }
}
