# Laravel Flexible Throttle

A Laravel middleware for advanced rate limiting based on IP,
HTTP status codes, and exceptions.
This package provides flexible and configurable rate limiting to protect
your application from abuse and ensure fair usage.

## Description

Laravel Flexible Throttle is designed to block intrusive bots
that scan all possible addresses or attempt to brute-force passwords and other parameters.
By leveraging IP addresses, session IDs, and user IDs,
this middleware can effectively limit the number of requests and block malicious activity.

## Features

- Rate limiting based on IP address, session ID, or user ID
- Configurable maximum attempts, decay seconds, and block duration
- Customizable rules for specific HTTP status codes and exceptions
- Easy integration with Laravel applications

## Installation

To install the package, use Composer:

```bash
composer require Wnikk/laravel-flexible-throttle
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Wnikk\FlexibleThrottle\FlexibleThrottleServiceProvider"
```

And edit the `config/flexible-throttle.php` file to customize the rate limiting.

## Usage

Apply the middleware to your routes:

```php
Route::middleware('flexible.throttle')->group(function () {
    Route::get('/example', 'ExampleController@index');
});
```

In this example, the middleware checks if the request has exceeded the maximum number of attempts 403 response. Then, it returns a `429 Too Many Requests` response. Otherwise, it records the request and allows it to proceed.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---
