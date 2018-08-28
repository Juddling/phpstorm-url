## Easily open your Laravel files in PHPStorm

This package takes a URL, works out the controller and action that the route is bound to, and opens that file at the correct line in PHPStorm

## Installation

```
composer require juddling/phpstorm-url --dev
```

### Add Service Provider (<= 5.4)
Be sure to include our service provider in your `app.php`:

```php
Juddling\PHPStorm\LaunchUrlServiceProvider::class,
```

## Usage

```
php artisan phpstorm:url http://someproject.localhost/forum/comment/add
```

We would recommend setting up a bash alias like the following:

```
alias purl="php artisan phpstorm:url"
```