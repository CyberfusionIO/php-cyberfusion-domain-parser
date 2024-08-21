# php-cyberfusion-domain-parser

Library to parse domains into their subdomain, SLD, TLD, and registrable domain.

This library is a wrapper around [`jeremykendall/php-domain-parser`](https://github.com/jeremykendall/php-domain-parser), focussed on ease of use.

# Install

## Composer

Run the following command to install the package from Packagist:

    composer require cyberfusion/domain-parser

# Usage

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

## Providers

### [Public Suffix List](https://publicsuffix.org/) (recommended)

```php
$parser = new Parser(provider: new PublicSuffixList());
```

### [IANA](https://data.iana.org/TLD/tlds-alpha-by-domain.txt)

```php
$parser = new Parser(provider: new IANATopLevelDomainList());
```

## Caching

This package caches data. to prevent too many requests to providers. You can provide your own cache to `Parser`, or use the included file cache.

For example, use the default cache store in Laravel:

```php
$parser = new Parser(
    cache: Cache::store(),
    provider: new PublicSuffixList()
);
```

