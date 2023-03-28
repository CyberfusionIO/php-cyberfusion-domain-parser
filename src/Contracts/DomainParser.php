<?php

namespace Cyberfusion\DomainParser\Contracts;

use Cyberfusion\DomainParser\Data\ParsedDomain;

interface DomainParser
{
    public function domain(string $domainName): ParsedDomain;
}
