<?php

namespace Cyberfusion\DomainParser\Data;

use Illuminate\Support\Str;

class ParsedDomain
{
    public function __construct(
        private readonly string $registrableDomain,
        private readonly string $tld,
        private readonly ?string $subdomain = null,
    ) {
    }

    public function getSld(): string
    {
        return Str::before($this->registrableDomain, sprintf('.%s', $this->tld));
    }

    public function getSubdomain(): ?string
    {
        return $this->subdomain;
    }

    public function hasSubdomain(): bool
    {
        return ! empty($this->subdomain);
    }

    public function getRegistrableDomain(): string
    {
        return $this->registrableDomain;
    }

    public function getTld(): string
    {
        return $this->tld;
    }

    public function getFqdn(): string
    {
        if (! $this->hasSubdomain()) {
            return $this->getRegistrableDomain();
        }

        return sprintf('%s.%s', $this->getSubdomain(), $this->getRegistrableDomain());
    }

    public function isApexDomain(): bool
    {
        return ! $this->hasSubdomain();
    }
}
