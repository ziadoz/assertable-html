<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class AssertableFormTest extends TestCase
{
    public function test_assertable_form(): void
    {
        // Form Methods
        AssertableDocument::createFromString('<form method="get"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertMethod('get');

        AssertableDocument::createFromString('<form><input type="hidden" name="_method" value="put"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertHiddenInputMethod('input[name="_method"]', 'put');
    }

    // <form method="get" action="/foo/bar" enctype="multipart/form-data" id="foo" class="bar">
    // <!-- Inputs -->
    // <label>Name <input type="text" name="name" value="Foo Bar"></label>
    // <label>Age <input type="number" name="age" value="42"></label>
    //
    // <!-- Upload Inputs -->
    // <label>File <input type="file" name="file"></label>
    //
    // <!-- Submits -->
    // <button type="submit">Save</button>
    // </form>

}
