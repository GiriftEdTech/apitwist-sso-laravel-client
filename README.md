
# This is the client Integration with ApiTwist SSO.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/Girift/apitwist-sso-laravel-client.svg?style=flat-square)](https://packagist.org/packages/Girift/apitwist-sso-laravel-client)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/Girift/apitwist-sso-laravel-client/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/Girift/apitwist-sso-laravel-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/Girift/apitwist-sso-laravel-client.svg?style=flat-square)](https://packagist.org/packages/Girift/apitwist-sso-laravel-client)

Client integration for ApiTwist SSO.

## Installation

Install the package via composer:

```bash
composer require girift/apitwist-sso-laravel-client
```

Add SSO config to your `.env` file:

```php
SSO_CLIENT_ID=client_id
SSO_CLIENT_SECRET=client_secret
SSO_DOMAIN='https://sso.apitwist.com'
```

Add HasSsoTokens trait to your User model

```php
// ...
use Girift\SSO\Traits\HasSsoTokens;

class User extends Authenticatable
{
    use HasSsoTokens;
    // ...
}
```

Add middlewares to your `app/Http/Kernel.php` file $routeMiddleware array:

```php
protected $routeMiddleware = [
    // ...
    'sso.auth' => \Girift\SSO\Http\Middleware\SsoAuthenticate::class,
    'sso.api' => \Girift\SSO\Http\Middleware\SsoApiAuthenticate::class,
];
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="sso-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="sso-config"
```

This is the contents of the published config file:

```php
return [
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_url' => config('app.url') . '/sso/callback',
    'sso_domain' => env('SSO_DOMAIN', 'https://sso.apitwist.com'),
    'authorize_url' => config('sso.sso_domain').'/oauth/authorize',
    'api_url' => config('sso.sso_domain').'/oauth/token',
    'logout_url' => config('sso.sso_domain'). '/logout',
    'get_user_url' => config('sso.sso_domain').  '/api/user',
];
```

## Usage

Use `sso.auth` along with `web` middleware in your `routes/web.php` file:

```php
Route::middleware([ 'web', 'sso.auth' ])->get('/route', function () {
    // Your routes
});
```

If you see `Session store not set on request.` error, add theese middlewares in your `app/Http/Kernel.php` file $middleware array:

```php
protected $middleware = [
    // ...
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
];
```

Use named routes as authentication routes:

```php
sso.login
sso.logout
```

Add `sso.loggedIn` named route to your home page:

```php
Route::middleware([ 'web', 'sso.auth' ])->get('/home', function () {
    // Your home page
})->name('sso.loggedIn')->name('home');
```


## Credits

- [Yasin BARAN](https://github.com/brnysn)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
