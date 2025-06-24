<?php

declare(strict_types=1);

namespace Ziadoz\AssertableHtml\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\Elements\AssertableForm;

class AssertableFormTest extends TestCase
{
    public function test_assertable_form(): void
    {
        $this->markTestSkipped('@todo');

        $html = AssertableDocument::createFromString(<<<'HTML'
        <form method="get" action="/foo/bar" enctype="multipart/form-data">
            <label>Name <input type="text" name="name" value="Foo Bar"></label>
            <label>Age <input type="number" name="age" value="42"></label>
            <button type="submit">Save</button>
        </form>
        HTML, LIBXML_HTML_NOIMPLIED);

        $html->one('form', function (AssertableForm $form) {
            $form->dump();
        });
    }
}
