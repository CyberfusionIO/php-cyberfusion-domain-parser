# Domain parser

This package is an easy-to-use wrapper around [jeremykendall/php-domain-parser](https://github.com/jeremykendall/php-domain-parser). It can be used to parse a domain into its subdomain, SLD, TLD, and registrable domain.

# Usage

## Requirements

This package requires Laravel 10+ and PHP 8.3 or higher.

## Installation

You can install the package via composer:

```bash
composer require cyberfusion/domain-parser
```

## Example

```php
use Cyberfusion\DomainParser\Parser;

$parser = new Parser();
$parsedDomain = $parser->domain('www.cyberfusion.nl');

$parsedDomain->getRegistrableDomain(); // cyberfusion.nl
$parsedDomain->getSld(); // cyberfusion
$parsedDomain->getTld(); // nl
$parsedDomain->hasSubdomain(): // true
$parsedDomain->getSubdomain(); // www
$parsedDomain->isApexDomain(); // false
$parsedDomain->getFqdn(); // www.cyberfusion.nl
```

### Providers

This package retrieves essential data from the IANA top level domain list or the Public suffix list. You can use one of 
the providers included in this package or even provide your own provider to tweak certain settings (but please do NOT 
perform too many requests to any of these sources, the data doesn't change that much anyway). 

```php
$parser = new Parser(provider: new PublicSuffixList());
```
or 
```php
$parser = new Parser(provider: new IANATopLevelDomainList());
```

### Caching

To prevent making a lot of requests to those sources, the package caches the data. You can provide your own cache to 
the `Parser` or use the default file cache which is included in this package.

For example, use the default cache store in Laravel:

```php
$parser = new Parser(
    cache: Cache::store(),
    provider: new PublicSuffixList()
);
```

## Tests

Unit tests are available in the `tests` directory. Run:

`composer test`

To generate a code coverage report in the `build/report` directory, run:

`composer test:coverage`

## Contributing

Contributions are welcome. See the [contributing guidelines](CONTRIBUTING.md).

## Security

If you discover any security related issues, please email support@cyberfusion.io instead of using the issue tracker.

## License

This client is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).