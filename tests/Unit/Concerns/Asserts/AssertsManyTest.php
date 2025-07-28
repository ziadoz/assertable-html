<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Unit\Concerns\Asserts;

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Concerns\AssertsMany;

class AssertsManyTest extends TestCase
{
    public function test_asserts_many(): void
    {
        try {
            $object = $this->getAssertsMany();
            $object->assertMany(function () {
                PHPUnit::assertSame('Foo', 'Foo');
                PHPUnit::assertSame('Foo', 'Bar', 'Foo is not Bar');
            }, 'The test assertion failed');
        } catch (AssertionFailedError $exception) {
            $this->assertSame('The test assertion failed', $exception->getMessage());
            $this->assertSame('Foo is not Bar' . "\n" . 'Failed asserting that two strings are identical.', $exception->getPrevious()->getMessage());
        }
    }

    private function getAssertsMany(): object
    {
        return new class
        {
            use AssertsMany;
        };
    }
}
