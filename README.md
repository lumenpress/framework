# LumenPress

## Installation

Installing Laravel or Lumen

```bash
composer create-project --prefer-dist laravel/laravel your-project && cd your-project
# or
composer create-project --prefer-dist laravel/lumen your-project && cd your-project
```

Installing `lumenpress/framework` package

```
composer require lumenpress/framework
```

Register the provider in `bootstrap/app.php`:

```php
$app->register(LumenPress\Providers\LumenPressServiceProvider::class);
```

Initialize wordpress

```bash
php artisan wp:init
```
