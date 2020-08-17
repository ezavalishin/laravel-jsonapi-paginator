# laravel-jsonapi-paginator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![StyleCI][ico-styleci]][link-styleci]
[![Codecov](https://img.shields.io/codecov/c/github/ezavalishin/laravel-jsonapi-paginator)][link-codecov]
[![Travis (.com)](https://img.shields.io/travis/com/ezavalishin/laravel-jsonapi-paginator)][link-travis]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require ezavalishin/laravel-jsonapi-paginator
```

Optionally you can publish the config file with:

```bash
$ php artisan vendor:publish --provider="ezavalishin/LaravelJsonApiPaginator\LaravelJsonApiPaginatorServiceProvider" --tag="config"
```

## Usage

To paginate the results according to the json API spec, simply call the jsonPaginate method.

```php
YourModel::jsonApiPaginate();
```

Of course you may still use all the builder methods you know and love:

```php
YourModel::where('my_field', 'myValue')->jsonApiPaginate();
```

By default the maximum page size is set to 30. You can change this number in the config file or just pass the value to jsonPaginate.

```php
$maxResults = 60;

YourModel::jsonApiPaginate($maxResults);
```

### Offset based pagination

Supports: `?page[offset]` and `?page[limit]`

### Page based pagination

Supports: `?page[number]` and `?page[size]`

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email ezavalishin@gmail.com instead of using the issue tracker.

## Credits

- [Evgeniy Zavalishin][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ezavalishin/laravel-jsonapi-paginator.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ezavalishin/laravel-jsonapi-paginator.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/287824410/shield

[link-packagist]: https://packagist.org/packages/ezavalishin/laravel-jsonapi-paginator
[link-downloads]: https://packagist.org/packages/ezavalishin/laravel-jsonapi-paginator
[link-styleci]: https://styleci.io/repos/287824410
[link-author]: https://github.com/ezavalishin
[link-contributors]: ../../contributors
[link-travis]: https://travis-ci.org/ezavalishin/laravel-jsonapi-paginator
[link-codecov]: https://codecov.io/github/ezavalishin/laravel-jsonapi-paginator
