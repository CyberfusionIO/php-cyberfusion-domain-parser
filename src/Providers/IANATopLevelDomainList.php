<?php

namespace Cyberfusion\DomainParser\Providers;

use Cyberfusion\DomainParser\Contracts\Provider;

class IANATopLevelDomainList implements Provider
{
    public function url(): string
    {
        return 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt';
    }

    public function identifier(): string
    {
        return 'iana';
    }

    public function ttl(): int
    {
        return 86400;
    }
}
