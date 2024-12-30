<?php

namespace Ziadoz\AssertableHtml\Tests\Elements;

use PHPUnit\Framework\AssertionFailedError;
use Ziadoz\AssertableHtml\Elements\AssertableFormElement;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableFormElementTest extends TestCase
{
    public function test_assert_method_get_passes(): void
    {
        new AssertableFormElement($this->getTestElement('<form method="GET"></form>'))->assertMethodGet();
    }

    public function test_assert_method_get_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [form] attribute [method] doesn't equal GET.");

        new AssertableFormElement($this->getTestElement('<form method="POST"></form>'))->assertMethodGet();
    }

    public function test_assert_method_post_passes(): void
    {
        new AssertableFormElement($this->getTestElement('<form method="POST"></form>'))->assertMethodPost();
    }

    public function test_assert_method_post_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [form] attribute [method] doesn't equal POST.");

        new AssertableFormElement($this->getTestElement('<form method="GET"></form>'))->assertMethodPost();
    }

    public function test_assert_method_accepts_uploads_passes(): void
    {
        new AssertableFormElement($this->getTestElement('<form method="POST" enctype="multipart/form-data"></form>'))
            ->assertAcceptsUploads();
    }

    public function test_assert_method_accepts_uploads_fails(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage("The element [form] attribute [enctype] doesn't equal multipart/form-data.");

        new AssertableFormElement($this->getTestElement('<form method="POST" enctype="text/plain"></form>'))
            ->assertAcceptsUploads();
    }
}
