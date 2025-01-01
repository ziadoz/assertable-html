<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Elements;

use Ziadoz\AssertableHtml\Elements\AssertableInputElement;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableInputElementTest extends TestCase
{
    public function test_assertable_input_element(): void
    {
        new AssertableInputElement($this->getTestElement('<div><input type="text"></div>'), 'input')
            ->assertType('text');

        new AssertableInputElement($this->getTestElement('<div><input name="foo[bar][baz]"></div>'), 'input')
            ->assertNameEquals('foo[bar][baz]');

        new AssertableInputElement($this->getTestElement('<div><input name="foo[bar][baz]"></div>'), 'input')
            ->assertNameStartsWith('foo[bar]');

        new AssertableInputElement($this->getTestElement('<div><input value="Foo Bar"></div>'), 'input')
            ->assertValueEquals('Foo Bar');

        new AssertableInputElement($this->getTestElement('<div><input value="Foo Bar"></div>'), 'input')
            ->assertValueDoesntEqual('Baz');

        new AssertableInputElement($this->getTestElement('<div><input value="Foo Bar"></div>'), 'input')
            ->assertValueContains('Bar');

        new AssertableInputElement($this->getTestElement('<div><input value="Foo Bar"></div>'), 'input')
            ->assertValueDoesntContain('Baz');

        new AssertableInputElement($this->getTestElement('<div><input type="checkbox" checked></div>'), 'input')
            ->assertChecked();

        new AssertableInputElement($this->getTestElement('<div><input type="checkbox"></div>'), 'input')
            ->assertUnchecked();

        new AssertableInputElement($this->getTestElement('<div><input type="checkbox" disabled></div>'), 'input')
            ->assertDisabled();

        new AssertableInputElement($this->getTestElement('<div><input type="checkbox"></div>'), 'input')
            ->assertEnabled();
    }
}
