<?php

namespace Ziadoz\AssertableHtml\Tests\Unit\Elements;

use PHPUnit\Framework\AssertionFailedError;
use Ziadoz\AssertableHtml\Elements\AssertableFormElement;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableFormElementTest extends TestCase
{
    public function test_assert_method_get_passes(): void
    {
        new AssertableFormElement($this->getTestElement('<div><form method="GET"></form></div>'), 'form')
            ->assertMethodGet();
    }

    public function test_assert_method_get_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [form] attribute [method] isn't the allowed value [get].");

        new AssertableFormElement($this->getTestElement('<div><form method="POST"></form></div>'), 'form')
            ->assertMethodGet();
    }

    public function test_assert_method_post_passes(): void
    {
        new AssertableFormElement($this->getTestElement('<div><form method="POST"></form></div>'), 'form')
            ->assertMethodPost();
    }

    public function test_assert_method_post_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [form] attribute [method] isn't the allowed value [post].");

        new AssertableFormElement($this->getTestElement('<div><form method="GET"></form></div>'), 'form')
            ->assertMethodPost();
    }

    public function test_assert_action_equals_passes(): void
    {
        new AssertableFormElement($this->getTestElement('<div><form action="/foo/bar"></form></div>'), 'form')
            ->assertActionEquals('/foo/bar');
    }

    public function test_assert_action_equals_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [form] attribute [action] doesn't equal the given value [/baz/qux].");

        new AssertableFormElement($this->getTestElement('<div><form action="/foo/bar"></form></div>'), 'form')
            ->assertActionEquals('/baz/qux');
    }

    public function test_assert_method_accepts_uploads_passes(): void
    {
        new AssertableFormElement($this->getTestElement('<div><form method="POST" enctype="multipart/form-data"></form></div>'), 'form')
            ->assertAcceptsUploads();
    }

    public function test_assert_method_accepts_uploads_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [form] attribute [enctype] isn't the allowed value [multipart/form-data].");

        new AssertableFormElement($this->getTestElement('<div><form method="POST" enctype="text/plain"></form></div>'), 'form')
            ->assertAcceptsUploads();
    }
}
