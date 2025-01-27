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


## 🚀 Get Started

### Requirements

- PHP 8.4+
- Composer
- Laravel 11+ (if applicable)

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
    
    public function testHtml(): void
    {
        // Available methods: assertableHtml(), assertHtml(), assertHead(), assertBody(), assertElement()
    
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

Assertable HTML mixes in several new methods onto the `TestResponse`, `TestView` and `TestComponent` classes in Laravel:

```php
// Responses...
// Available methods: assertableHtml(), assertHtml(), assertHead(), assertBody(), assertElement()
public function testResponse(): void
{
    /* 
    <div>
        <h1>Welcome, Archie!</h1>
    </div>
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
        <input name="action" value="My New Action" class="form-input" required>form-input
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

## ⏰ Quick Start

Here's a quick example of what you can do with Assertable HTML:

```php
use Ziadoz\AssertableHtml\Dom\AssertableDocument;
use Ziadoz\AssertableHtml\Dom\AssertableElement;

// Make the HTML response an assertable document...
$html = AssertableDocument::createFromString(<<<HTML
    <h1 class="heading">Dashboard</h1>

    <nav>
        <a href="/dashboard" class="nav-link active">Dashboard</a>
        <a href="/settings" class="nav-link">Settings</a>
        <a href="/logout" class="nav-link">Logout</a>
    </nav>

    <div id="content">
        <h2 class="heading subheading">Welcome, Archie</h2>

        <search>
            <form method="post" action="/search">
                <!-- ... -->

                <input type="hidden" name="csrf" value="...">
                <button type="Submit">Search</button>
            </form>
        </search>
    </div>
HTML, LIBXML_NOERROR);

// Assert on the <h1> element...
$html->querySelector('h1')->assertTextEquals('Dashboard');

// Start asserting on the <nav> element...
$html->with('nav', function (AssertableElement $nav) {
    // Assert there are 3 navigation links with the correct classes...
    $links = $nav->querySelectorAll('a')
        ->assertCount(3)
        ->assertAll(function (AssertableElement $a) {
            $a->classes->contains('nav-link');
        });

    // Assert each link has the correct URL and "active" style class...
    $links[0]->assertAttributeEquals('href', '/dashboard')
        ->assertTextEquals('Dashboard')
        ->assertClassContains('active');

    $links[1]->assertAttributeEquals('href', '/settings')
        ->assertTextEquals('Settings')
        ->assertClassDoesntContain('active');

    $links[2]->assertAttributeEquals('href', '/logout')
        ->assertTextEquals('Logout')
        ->assertClassDoesntContain('active');

    // More assertions within <nav>...
});

// Start asserting on the <div id="content"> element...
$html->with('#content', function (AssertableElement $div) {
    // Assert the sub-heading...
    $div->querySelector('h2')
        ->assertClassContainsAll(['heading', 'subheading'])
        ->assertTextContains('Welcome');

    $div->with('search form', function (AssertableElement $form) {
        // Assert the form posts to the correct endpoint...
        $form->assertAttributeEquals('method', 'post')
            ->assertAttributeStartsWith('action', '/search');

        // Assert there's a submit button and hidden CSRF token...
        $form->assertElementsExist('button[type="submit"]')
            ->assertElementsExist('input[type="hidden"][name="csrf"]');
    });

    // More assertions within <div id="content">...
});

```

## 🔨 Usage

### Basics

### Assertions

### Advanced Assertions

## 👏 Thanks

This package wouldn't be possible without the following people and projects:

- Rachel ❤️, Archie 🐶 and Rigby 🐶
- Niels Dossche (PHP 8.4 HTML parsing API author)
- [Laravel DOM Assertions](https://github.com/sinnbeck/laravel-dom-assertions) for showing me the possibilities of HTML assertions
- [Laravel Dusk](https://github.com/laravel/dusk) for showing me the `with()` and `elsewhere()` scoping syntax
- [Lexbor](https://github.com/lexbor/lexbor) (the library that powers PHP 8.4's HTML parsing API)
