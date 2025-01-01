<?php

namespace Ziadoz\AssertableHtml\Tests\Unit\Elements;

use Ziadoz\AssertableHtml\Elements\AssertableFormElement;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableFormElementTest extends TestCase
{
    public function test_assertable_form_element(): void
    {
        new AssertableFormElement($this->getTestElement('<div><form method="GET"></form></div>'), 'form')
            ->assertMethodGet();

        new AssertableFormElement($this->getTestElement('<div><form method="POST"></form></div>'), 'form')
            ->assertMethodPost();

        new AssertableFormElement($this->getTestElement('<div><form action="/foo/bar"></form></div>'), 'form')
            ->assertActionEquals('/foo/bar');

        new AssertableFormElement($this->getTestElement('<div><form enctype="multipart/form-data"></form></div>'), 'form')
            ->assertAcceptsUploads();
    }
}
