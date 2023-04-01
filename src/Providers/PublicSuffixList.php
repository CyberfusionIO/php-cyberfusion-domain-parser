<?php

namespace Cyberfusion\DomainParser\Providers;

use Cyberfusion\DomainParser\Contracts\Provider;

class PublicSuffixList implements Provider
{
    public function url(): string
    {
        return 'https://publicsuffix.org/list/public_suffix_list.dat';
    }

    public function identifier(): string
    {
        return 'public-suffix-list';
    }

    public function ttl(): int
    {
        return 86400;
    }
}
