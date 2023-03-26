# domain-parser

PHP library to parse domains.

# Usage

## Example

```php
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