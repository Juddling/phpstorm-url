## PHPStorm launch url from command line

Note, this is only for Laravel projects.

This packages takes a URL, works out the controller and action that the route is bound to, and opens that file and line in PHPStorm

## Installation

```
composer require juddling/phpstorm-url --dev
```

Be sure to include our service provider in your `app.php`:

```php
\Juddling\PHPStorm\MusterServiceProvider::class
```

## Usage

`php artisan phpstorm:url http://someproject.dev/forum/comment/add`