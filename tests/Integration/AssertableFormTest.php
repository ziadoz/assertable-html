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
        AssertableDocument::createFromString('<form method="GET"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertMethodGet();

        AssertableDocument::createFromString('<form method="POST"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertMethodPost();

        AssertableDocument::createFromString('<form method="DIALOG"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertMethodDialog();

        AssertableDocument::createFromString('<form><input type="hidden" name="_method" value="PUT"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertMethodPut();

        AssertableDocument::createFromString('<form><input type="hidden" name="_method" value="PATCH"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertMethodPatch();

        AssertableDocument::createFromString('<form><input type="hidden" name="_method" value="DELETE"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertMethodDelete();

        // Form Uploads
        AssertableDocument::createFromString('<form enctype="multipart/form-data"><input type="file"></form>', LIBXML_HTML_NOIMPLIED)
            ->querySelector('form')
            ->assertAcceptsUpload();
    }
}
