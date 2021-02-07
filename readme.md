Elastic APM PHP
---

:warning: **WARNING: The package dependency [`ext-elastic_apm`](https://github.com/elastic/apm-agent-php) is in development mode that pushes breaking changes now and then. Also discouraged to use in a production environment. Thus this project will unmaintained till the stability of the dependency gets resolved. Feel free to fork and change accordingly. Or use any available alternative**

## Requirements
- The package depends on elastic's [apm-agent-php](https://github.com/elastic/apm-agent-php) extension.
- php `^7.2`
- If want to use with Laravel, Laravel version >= `6.x`.

## Installation
To install the package with composer, run:
```shell script
composer require anik/elastic-apm-php
```
Use the appropriate version while installing if you want any specific version. Above command will install the latest available version.

### Laravel
- This package uses Laravel's auto discovery feature. But, if you still want to install, then
    1. Add `Anik\ElasticApm\Providers\ElasticApmServiceProvider::class` in your `config/app.php`'s providers array.
    2. Add `Anik\ElasticApm\Facades\Agent::class` in your `config/app.php`'s facade array.
    3. `php artisan vendor:publish` and select the provider to publish the config file in your config directory. It'll copy `elastic-apm.php` in your config directory.

### Lumen
- To install this package with your lumen, you don't need to enable **Facade**.
- Register the service provider in your `bootstrap/app.php` with `$app->register(Anik\ElasticApm\Providers\ElasticApmServiceProvider::class);`
- Copy the `elastic-apm.php` from `vendor/anik/elastic-apm-php/src/config/elastic-apm.php` in your `config/app.php` file if you want to change the configuration.
- Register the copied config file `$app->configure('elastic-apm')` in your `bootstrap/app.php`

## Usage

Error Tracking
---
If you want to keep the records of Errors, then
- For Laravel, in your `bootstrap/app.php`,
```php
// COMMENT THIS SECTION
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);
```
```php
// USE THIS SECTION FOR LARAVEL <= 7
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, function ($app) {
    return new Anik\ElasticApm\Exceptions\Handler(new App\Exceptions\Handler($app), [
        // NotFoundHttpException::class, // (1)
        // ConnectException::class, // (2)
    ]);
});
```

```php
// USE THIS SECTION FOR LARAVEL >= 8
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, function ($app) {
    return new Anik\ElasticApm\Exceptions\HandlerThrowable(new App\Exceptions\Handler($app), [
        // NotFoundHttpException::class, // (1)
        // ConnectException::class, // (2)
    ]);
});
```

- For Lumen, in your `bootstrap/app.php`,
```php
// COMMENT THIS SECTION
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);
```

```php
// USE THIS SECTION FOR LUMEN <= 7
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, function ($app) {
    return new Anik\ElasticApm\Exceptions\Handler(new App\Exceptions\Handler(), [
        // NotFoundHttpException::class, // (1)
        // ConnectException::class, // (2)
    ]);
});
```

```php
// USE THIS SECTION FOR LUMEN >= 8
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, function ($app) {
    return new Anik\ElasticApm\Exceptions\HandlerThrowable(new App\Exceptions\Handler(), [
        // NotFoundHttpException::class, // (1)
        // ConnectException::class, // (2)
    ]);
});
```

Request Response Tracking
---
If you want to keep the records of Request received and served by your application,
- For Laravel, in your `App\Http\Kernel`'s middleware, add `Anik\ElasticApm\Middleware\RecordForegroundTransaction` class.
```php
<?php

class Kernel extends HttpKernel {
    protected $middleware = [
        // ...
        \Anik\ElasticApm\Middleware\RecordForegroundTransaction::class,
        // ..
    ];
}
```

- For Lumen, in your `bootstrap/app.php` file, add `Anik\ElasticApm\Middleware\RecordForegroundTransaction` class.
```php
$app->middleware([
    // ...
    \Anik\ElasticApm\Middleware\RecordForegroundTransaction::class,
    // ...
]);
```

Background Job Tracking
---
For both the Laravel & Lumen, you'll have to add `Anik\ElasticApm\Middleware\RecordBackgroundTransaction` class as your Job's middleware.

HTTP Call tracking
---
To track the HTTP calls, you'll need to use Guzzle. You can pass the `RecordHttpTransaction` class as your Guzzle's handler stack. It'll then record HTTP calls as well.
```php
$stack = \GuzzleHttp\HandlerStack::create();
$stack->push(new \Anik\ElasticApm\Middleware\RecordHttpTransaction(), 'whatever-you-wish');

$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://httpbin.org',
    'timeout'  => 10.0,
    'handler'  => $stack,
]);
$client->request('GET', '/');
```


## Documentation
Please check the given URL in the project description to get a thorough view of what you can do with it. And how to customize it.

## PRs?
If you find any bug and update, please make sure you send a PR. PRs are always appreciated.
