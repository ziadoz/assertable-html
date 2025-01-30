![Assertable HTML](art/logo.png)

----

Assertable HTML takes a fresh approach to testing HTML responses generated by templating engines such as Blade and Twig in PHP and Laravel web applications. It provides an elegant interface that allows developers to fluently navigate and target their HTML using modern CSS selectors, and then write effective test assertions against the results.

**Key Features:**

- **Fluent Interface:** Navigate and target HTML elements using an API similar to native JavaScript.
- **Minimal Interface:** Work with a stripped back API that’s focused on testing.
- **Chainable Assertions:** Quickly chain together multiple assertions on elements.
- **Flexible Assertions:** Supply callbacks for complex element assertions if the built-in ones aren’t sufficient.
- **Element-Specific Assertions:** Use element-specific assertions to quick test forms and more (**Coming Soon**).

## Table Of Contents

- [Get Started](#-get-started)
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [PHPUnit Installation](#phpunit-installation)
  - [Laravel Installation](#laravel-installation)
- [Usage](#-usage)
  - [Basics](#basics)
  - [Scopes](#scopes)
  - [Assertions](#assertions)
  - [Assertion Messages](#assertion-messages) 
  - [Flexible Assertions](#flexible-assertions)
  - [Output HTML](#output-html)

## 🚀 Get Started

### Requirements

- PHP 8.4+
- Composer
- Laravel >=11.41.0 (if applicable) 

### Installation

You can install the package using Composer:

```bash
composer install ziadoz/assertable-html
```

### PHPUnit Installation

If you're using PHPUnit, simply include the trait in your test class:

```php
<?php

use PHPUnit\Framework\TestCase;
use Ziadoz\AssertableHtml\Traits\AssertsHtml;
use Ziadoz\AssertableHtml\Dom\AssertableDocument;

class MyTest extends TestCase
{
    use AssertsHtml;
    
    // Available methods: assertableHtml(), assertHtml(), assertHead(), assertBody(), assertElement()
    public function testHtml(): void
    {        
        $html = <<<'HTML'
        <html>
            <body>
                <h1>Welcome, Archie!</h1>
            </body>
        </html>
        HTML;
        
        $this->assertHtml($html, function (AssertableDocument $html) {
            $html->querySelector('h1')
                ->assertTextEquals('Welcome, Archie!');
        });
    }
}
```

Alternatively you can use the `Ziadoz\AssertableHtml\Dom\AssertableDocument::createFromString()` and `Ziadoz\AssertableHtml\Dom\AssertableDocument::createFromFile()` methods directly as needed:

```php
public function testHtml(): void
{
    AssertableDocument::createFromString('<p>Foo</p>', LIBXML_HTML_NOIMPLIED)
        ->querySelector('p')
        ->assertTextEquals('Foo');
}
```

### Laravel Installation

If you're using Laravel, Assertable HTML will be automatically discovered, however you can register it manually if needed:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ziadoz\AssertableHtml\AssertableHtmlServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(AssertableHtmlServiceProvider::class);
    }
}
```

Assertable HTML adds several new methods to the `TestResponse`, `TestView` and `TestComponent` classes in Laravel:

```php
// Responses...
// Available methods: assertableHtml(), assertHtml(), assertHead(), assertBody(), assertElement()
public function testResponse(): void
{
    /* 
    <html>
        <body>
            <h1>Welcome, Archie!</h1>
        </body>
    </html>
    */

    $this->get('/')->assertBody(function (AssertableElement $body) {
        $body->querySelector('h1')
            ->assertTextEquals('Welcome, Archie!');
    });
};

// Views...
// Available methods: assertableHtml(), assertElement()
public function testView(): void
{ 
    /*
    <nav>
        <ul>
            <li class="nav-link">Foo<li>
            <li class="nav-link active-link">Bar<li>
            <li class="nav-link">Baz<li>
            <li class="nav-link">Qux<li>
        </ul>
    </nav>
    */

    $this->view('nav')->assertElement(function (AssertableDocument $div) {
        $div->assertTag('div');

        $lis = $div->querySelectorAll('ul li')
            ->assertCount(4)
            ->assertAll(function (AssertableElement $li) {
                return $li->classes->contains('nav-link');
            });
        
        $lis[1]->assertClassContains('active-link');
    });
}

// Components...
// Available methods: assertableHtml(), assertElement()
// Note: Only available in Laravel >= 11.41.0
public function testComponent: void
{
    /*
    <form method="post" action="/foo/bar">
        <input name="action" value="My New Action" class="form-input" required>
        <!-- ... -->
    </form>
    */

    $this->component('action')->assertElement(function (AssertableDocument $form) {
        $form->assertTag('form')
            ->assertAttributeEquals('method', 'post')
            ->assertAttributeEquals('action', '/foo/bar');
        
        $form->with('input[name="name"]', function (AssertableElement $input) {
            $input->assertAttributeEquals('value', 'My New Action');
            $input->assertAttributePresent('required');
            $input->assertClassContains('form-input');
        });
    });
}
```

## 🔨 Usage

### Basics

To start performing assertions on HTML, create an assertable document:

```php
$document = AssertableDocument::createFromString(<<<'HTML'
    <ul>
        <li id="foo" class="foo" data-foo="foo"><strong>Foo</strong></li>
        <li id="bar" class="bar" data-bar="bar"><strong>Bar</strong></li>
        <li id="baz" class="baz" data-baz="baz"><strong>Baz</strong></li>
        <li id="qux" class="qux" data-qux="foo"><strong>Qux</strong></li>
    </ul>
HTML, LIBXML_HTML_NOIMPLIED);
```

Now you can begin performing queries using `querySelector()` and `querySelectorAll()`, exactly like working with the DOM in JavaScript:

The `querySelector()` method will return a `Ziadoz\AssertableHtml\Dom\AssertableElement` instance containing the first matching element, which you can use to perform assertions on the element:

```php
$element = $document->querySelector('li:first-of-type');
$element->assertIdEquals('foo');
$element->assertTextEquals('Foo');
```

If there are no elements matching the selector, your test will fail:

```php
$element = $document->querySelector('foobar');
// The document doesn't contain an element matching the given selector [foobar].
```

Assertions are fluent, so if you prefer, you can chain then together:

```php
$element = $document->querySelector('li:first-of-type')
    ->assertIdEquals('foo')
    ->assertTextEquals('Foo');
```

An assertable element has a handful of properties you can access:

```php
echo $element->tag;                    // 'Foo'
echo $element->html;                   // '<strong>Foo</strong>''
echo $element->id;                     // 'foo'
echo $element->text;                   // 'Foo'
echo $element->classes->toArray();     // ['foo']
echo $elements->attributes->toArray(); // ['id' => 'foo', 'class' => 'foo']
```

The `text`, `classes` and `attributes` properties refer to further assertable classes:

- `text`: `Ziadoz\AssertableHtml\Dom\AssertableText`.
- `classes`: `Ziadoz\AssertableHtml\Dom\AssertableClassesList`.
- `attributes`: `Ziadoz\AssertableHtml\Dom\AssertableAttributesList`.

```php
// Text
echo $element->text->value();                         // '  Foo  Bar  '
echo $element->text->value(normaliseWhitespace: true) // 'Foo Bar'

// Classes
echo $element->classes->value();                          // '  foo  bar  '
echo $element->classes->value(normaliseWhitespace: true); // 'foo bar'

$element->classes->toArray();           // ['foo', 'bar']
$element->classes->empty();             // false
$element->classes->contains('foo');     // true
$element->classes->any(['foo', 'qux']); // true
$element->classes->all(['foo', 'qux']); // false

$element->classes->each(function (string $class, int $index) {
    echo $class; // 'foo'
    echo $index; // 0
});

$element->classes->sequence(
    fn (string $class, int $sequence): => $this->assertSame('foo', $class),
    fn (string $class, int $sequence): => $this->assertSame('bar', $class),
);

// Attributes
echo $element->attributes->value('data-foo');                            // '  bar  '
echo $element->attributes->value('data-foo', normaliseWhitespace: true); // 'bar'

$element->attributes->toArray();       // ['class' => 'foo bar', 'data-foo' => 'bar']
$element->attributes->empty();         // false
$element->attributes->names();         // ['class', 'data-foo']
$element->attributes->has('data-foo'); // true

$element->attributes->each(function (string $attribute, ?string $value, int $index) {
    echo $attribute; // 'class'
    echo $value;     // 'foo-bar'
    echo $index;     // 0 
});

$element->attributes->sequence(
    fn (string $attribute, ?string $value, int $sequence): => $this->assertSame('class', $attribute),
    fn (string $attribute, ?string $value, int $sequence): => $this->assertSame('data-foo', $attribute),
);
```

You can perform assertions using these classes, however, in most cases the element has a proxy method that makes it more convenient to do from the element:

```php
$element->assertClassContains('foo');
$element->assertIdEquals('foo');
$element->assertAttributEquals('foo', 'foo');
```

These classes can be useful when you want to perform more advanced custom assertions.

The `querySelectorAll()` method returns a `Ziadoz\AssertableHtml\Dom\AssertableElementsList` instance containing every matching element, which allows you to work with the matching elements as an array:


```php
$elements = $document->querySelectorAll('ul > li');

// Access using methods...
echo $elements->first()->id; // foo
echo $elements->nth(1)->id   // bar
echo $elements->nth(2)->id   // baz
echo $elements->last()->id;  // qux

// Or regular array syntax...
echo $elements[0]->id; // foo
echo $elements[1]->id   // bar
echo $elements[2]->id   // baz
echo $elements[3]->id;  // qux
```

If there are no elements matching the selector, you'll still get back an `AssertableElementsList`, just in case you want to check there are no elements:

```php
$elements->assertEmpty();
$elements->assertNotEmpty();
```

You can perform assertions and chain assertions on the matching elements:

```php
$elements->assertCount(4);

$elements->assertAll(function (AssertableElmeent $element) {
    return $element->attributes->has('class');
})->assertAny(function (AssertableElmeent $element) {
    return $element->classes->contains('foo');
})

$element->each(function (AssertableElement $element) {
    $element->assertClassContains('foo');
});

$element->sequence(
    fn (AssertableElement $el, int $sequence) => $el->assertTextEquals('Foo'),
    fn (AssertableElement $el, int $sequence) => $el->assertTextEquals('Bar'),
    fn (AssertableElement $el, int $sequence) => $el->assertTextEquals('Baz'),
    fn (AssertableElement $el, int $sequence) => $el->assertTextEquals('Qux'),
);

$elements[0]->assertIdEquals('foo')->assertTextEquals('Foo');
$elements[1]->assertIdEquals('bar')->assertTextEquals('Bar');
$elements[2]->assertIdEquals('baz')->assertTextEquals('Baz');
$elements[3]->assertIdEquals('qux')->assertTextEquals('Qux');
```

You can also use `getElementsByTagName()` or `getElementById()` to query for elements if needed:

```php
$document->getElementsByTagName('li');
$document->getElementById('bar');
```

### Scopes

Sometimes your assertions need room to breathe. For this you can use `with()`, `many()`, `elsewhere()` and `scope()` to filter elements into a callback for better readability.

- `with()`: The first matching element in the **current** scope using `querySelector()`.
- `many()`: Every matching element in the **current** scope using `querySelectorAll()`,
- `elsewhere()`: The first matching element in the **document** scope using `querySelector()`.
- `scope()`: The current element.

Let's give them a try:

```php
$document = AssertableDocument::createFromString(<<<'HTML'
    <div id="outer">
        <div id="inner">
            <div class="innermost"></div>
            <div class="innermost"></div>
            <div class="innermost"></div>
        </div>

        <div id="another-inner">
            <div class="another-innermost"></div>
            <div class="another-innermost"></div>
            <div class="another-innermost"></div>
        </div>
    </div>
HTML, LIBXML_HTML_NOIMPLIED);

$document->with('div#inner', function (AssertableElement $inner) {
    $inner->assertIdEquals('inner');

    $inner->many('div.innermost', function (AssertableElementsList $innerMosts) {
        $innerMosts->assertCount(3);
    });

    $inner->elsewhere('div#another-inner', function (AssertableElement $anotherInner) {
        $anotherInner->assertIdEquals('another-inner');
    });

    $inner->scope(function (AssertableElement $inner) {
        $inner->assertIdEquals('inner');
    });
});
```

The `when()` method makes it possible to perform assertions conditionally, which can be useful when working with data providers or more complex tests:

```php
$element->when(
    // Condition can be a boolean, or a callable that evaluates to a boolean...
    $condition,                                                           
    // Called when condition is true...
    fn (AssertableElement $element) => $element->assertTextEquals('Foo'),
    // Called when condition is false... 
    fn (AssertableElement $element) => $element->assertTextEquals('Bar'),
);
```

### Assertions

Assertable HTML provides loads of assertions to help you test your HTML is exactly as expected. The majority of these assertions live on the `AssertableElement` instance, and can be categorised as follows:

- **Exists**: Assert one or many child elements do or don't exist.
- **Tag**: `Assert the element's tag.
- **Matches**: Assert the element does or doesn't match a selector.
- **Count**: Assert the number of child elements matching a selector.
- **Text**: Assert the element's text.
- **IDs:** Assert the element's ID attribute.
- **Classes:** Assert the element's classes.
- **Attributes:** Assert the element's attributes.

Here's an example:

```php
$element->assertTagEquals('div')
    ->assertIdEquals('foo');

$element->assertClassContains('heading')
    ->assertClassDoesntContain('subheading')
    ->assertTextContains('Welcome')
    ->assertTextDoesntContain('Foo!');

$element->assertElementsCount('li.bullet', 3);
```

If you're using an IDE such as PhpStorm or VSCode, it should auto-complete the dozens of assertions available for you, along with their parameters.

### Assertion Messages

All assertions include a final `$message` parameter, which allows you to customise the failure message in your tests for your application:

```php
$document->assertElementDoesntExist('img.avatar', 'The profile page is missing an avatar image.');
```

This can be useful when you need to identify test failures that are specific to your web application. 

### Flexible Assertions

Sometimes you have a scenario that just isn't possible to test with a built-in assertion. For those scenarios Assertable HTML provides various assertions that accept a callback. If the callback returns `true`, the test will pass, otherwise it will fail:

```php
$element->assertElement(function (AssertableElement $element) {
    return (
        $element->classes->contains('foo') &&
        str_contains('-Bar-', $element->text->value()) &&
        $element->attributes->has('data-foo');
    );
});
```

Every assertable class has flexible assertions available, just in case:

- `AssertableElement`: `assertElement()`, `assertText()`, `assertClass()`, `assertAttributes()` and `assertAttribute()`.
- `AssertableElementsList`: `assertElements()`.
- `AssertableClassesList`: `assertClasses()`.
- `AssertableAttributesList`: `assertAttributes()` and `assertAttribute()`.
- `AssertableText`: `assertText()`.

### HTML Output

If you ever need to see the HTML of the element(s) you're working with, you can call `dump()` and `dd()` on the assertable instance:

```php
$element->querySelector('p')->dump(); 
// <p>Foo</p>

$element->querySelectorAll('p, span')->dump();
// <p>Foo</p>
// <span>Bar</span>
```

You can also call `getHtml()` to retrieve the HTML as a string:

```php
echo $element->querySelector('p')->getHtml(); // <p>Foo</p>
```

## 👏 Thanks

This package wouldn't be possible without the following people and projects:

- Rachel ❤️, Archie 🐶 and Rigby 🐶
- Niels Dossche (PHP 8.4 HTML parsing API author)
- [Laravel DOM Assertions](https://github.com/sinnbeck/laravel-dom-assertions) for showing me the possibilities of HTML assertions
- [Laravel Dusk](https://github.com/laravel/dusk) for showing me the `with()` and `elsewhere()` scoping syntax
- [Lexbor](https://github.com/lexbor/lexbor) (the library that powers PHP 8.4's HTML parsing API)
