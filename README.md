# DocuWare integration with Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar/laravel-docuware)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar/laravel-docuware.svg?style=flat-square)](https://packagist.org/packages/codebar/laravel-docuware)
[![run-tests](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/codebar-ag/laravel-docuware/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/php-cs-fixer.yml)
[![Psalm](https://github.com/codebar-ag/laravel-docuware/actions/workflows/psalm.yml/badge.svg)](https://github.com/codebar-ag/laravel-docuware/actions/workflows/psalm.yml)


A simple way to integrate the DocuWare REST API with your Laravel application.

⚠️ This package is not designed to cover all endpoints. See the official 
[DocuWare REST API](https://developer.docuware.com/rest/index.html) 
documentation if you need further functionality.

## Local package testing

Copy your own phpunit.xml-file.
```bash
cp phpunit.xml.dist phpunit.xml
```

Modify environment variables in the phpunit.xml-file:
```xml
<env name="DOCUWARE_URL" value="https://domain.docuware.cloud"/>
<env name="DOCUWARE_USER" value="user@domain.test"/>
<env name="DOCUWARE_PASSWORD" value="password"/>
```

Run the tests
```bash
./vendor/bin/phpunit
```
   
## Installation

You can install the package via composer:

```bash
composer require codebar/laravel-docuware
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="codebar\DocuWare\DocuWareServiceProvider" --tag="docuware-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$laravel-docuware = new codebar\DocuWare();
echo $laravel-docuware->echoPhrase('Hello, codebar!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ruslan Steiger](https://github.com/SuddenlyRust)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
