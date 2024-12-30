<?php

namespace Ziadoz\AssertableHtml\Tests\Elements;

use Dom\HTMLDocument;
use Generator;
use Ziadoz\AssertableHtml\Elements\AssertableElement;
use Ziadoz\AssertableHtml\Elements\AssertableElementInterface;
use Ziadoz\AssertableHtml\Elements\AssertableElementsCollection;
use Ziadoz\AssertableHtml\Tests\TestCase;

class AssertableElementsCollectionTest extends TestCase
{
    public function test_nth(): void
    {
        $collection = $this->getTestCollection();

        $this->assertSame($collection[0], $collection->nth(0));
    }

    public function test_filter(): void
    {
        $filtered = $this->getTestCollection()->filter(function (AssertableElementInterface $element) {
            return $element->getRoot()->matches('.first, .third');
        });

        $this->assertCount(2, $filtered);
        $this->assertTrue($filtered[0]->getRoot()->matches('li.first'));
        $this->assertTrue($filtered[1]->getRoot()->matches('li.third'));
    }

    public function test_each(): void
    {
        $count = 0;

        $this->getTestCollection()->each(function (AssertableElementInterface $element) use (&$count) {
            $element->assertMatchesSelector('li');
            $count++;
        });

        $this->assertSame(4, $count);
    }

    public function test_slice(): void
    {
        $sliced = $this->getTestCollection()->slice(2);

        $this->assertCount(2, $sliced);
        $this->assertTrue($sliced[0]->getRoot()->matches('li.third'));
        $this->assertTrue($sliced[1]->getRoot()->matches('li.fourth'));

        $sliced = $this->getTestCollection()->slice(1, -1);

        $this->assertCount(2, $sliced);
        $this->assertTrue($sliced[0]->getRoot()->matches('li.second'));
        $this->assertTrue($sliced[1]->getRoot()->matches('li.third'));
    }

    /*
    |--------------------------------------------------------------------------
    | Array Access
    |--------------------------------------------------------------------------
    */

    public function test_array_access_offset_exists(): void
    {
        $collection = $this->getTestCollection();

        foreach ($collection as $position => $element) {
            $this->assertTrue(isset($collection[$position]));
        }

        $this->assertFalse(isset($collection[42]));
    }

    public function test_array_access_offset_get(): void
    {
        $collection = $this->getTestCollection();

        foreach ($collection as $position => $element) {
            $this->assertInstanceOf(AssertableElementInterface::class, $collection[$position]);
            $this->assertInstanceOf(AssertableElementInterface::class, $element);
        }
    }

    public function test_array_access_offset_set(): void
    {
        $collection = $this->getTestCollection();
        $collection[] = new AssertableElement($this->getFixtureElement('<div><p>Foo</p></div>'), 'p');
        $collection[42] = new AssertableElement($this->getFixtureElement('<div><p>Foo</p></div>'), 'p');

        $this->assertTrue(isset($collection[4]));
        $this->assertTrue(isset($collection[42]));
        $this->assertCount(6, $collection);
    }

    public function test_array_access_offset_unset(): void
    {
        $collection = $this->getTestCollection();

        unset($collection[2]);
        unset($collection[3]);

        $this->assertCount(2, $collection);
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    public function test_countable(): void
    {
        $collection = $this->getTestCollection();

        $this->assertCount(4, $collection);
        $this->assertCount(0, new AssertableElementsCollection);
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    public function test_get_iterator(): void
    {
        $collection = $this->getTestCollection();

        $this->assertInstanceOf(Generator::class, $collection->getIterator());
    }

    protected function getTestCollection(): AssertableElementsCollection
    {
        $ul = HTMLDocument::createFromString(<<<'HTML'
            <ul class="list">
                <li class="first"><span>Foo</span></li>
                <li class="second"><span>Bar</span></li>
                <li class="third"><span>Baz</span></li>
                <li class="fourth"><span>Qux</span></li>
            </ul>
        HTML, LIBXML_NOERROR)->querySelector('ul');

        return new AssertableElementsCollection([
            new AssertableElement($ul, 'li.first'),
            new AssertableElement($ul, 'li.second'),
            new AssertableElement($ul, 'li.third'),
            new AssertableElement($ul, 'li.fourth'),
        ]);
    }
}
