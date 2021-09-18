# Laravel-Plugins

## Install

To install through Composer, by run the following command:

``` bash
composer require hyperbolaa/laravel-plugins
```

The package will automatically register a service provider and alias.

Optionally, publish the package's configuration file by running:

``` bash
php artisan vendor:publish --provider="Hyperbolaa\Plugins\LaravelPluginsServiceProvider"
```

### Autoloading

By default, the module classes are not loaded automatically. You can autoload your plugins using `psr-4`. For example:

``` json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Plugins\\": "Plugins/"
    }
  }
}
```

**Tip: don't forget to run `composer dump-autoload` afterwards.**

## Documentation

You'll find installation instructions and full documentation on [https://fresns.cn/plugins/](https://fresns.cn/plugins/).


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
