<?php

namespace Cyberfusion\DomainParser;

use Cyberfusion\DomainParser\Contracts\DomainParser;
use Cyberfusion\DomainParser\Contracts\Provider;
use Cyberfusion\DomainParser\Data\ParsedDomain;
use Cyberfusion\DomainParser\Exceptions\DomainParserException;
use Cyberfusion\DomainParser\Providers\IANATopLevelDomainList;
use Cyberfusion\DomainParser\Providers\PublicSuffixList;
use Desarrolla2\Cache\File;
use GuzzleHttp\Client;
use Pdp\CannotProcessHost;
use Pdp\Domain;
use Pdp\Rules;
use Pdp\TopLevelDomains;
use Pdp\UnableToLoadPublicSuffixList;
use Pdp\UnableToLoadTopLevelDomainList;
use Pdp\UnableToResolveDomain;
use Psr\SimpleCache\CacheInterface;
use Throwable;

class Parser implements DomainParser
{
    public function __construct(
        private CacheInterface|null $cache = null,
        private ?Provider $provider = null,
    ) {
        if ($this->cache === null) {
            $this->cache = new File(__DIR__);
        }
        if ($this->provider === null) {
            $this->provider = new PublicSuffixList();
        }
    }

    /**
     * @throws DomainParserException
     */
    private function retrieveTopLevelDomainList(): string
    {
        $client = new Client([
            'connect_timeout' => 10,
            'timeout' => 30,
        ]);
        try {
            $response = $client->get($this->provider->url());
        } catch (Throwable $exception) {
            throw DomainParserException::unableToLoadSourceList(
                error: $exception->getMessage(),
                previous: $exception,
            );
        }

        return (string) $response->getBody();
    }

    /**
     * @throws DomainParserException
     */
    private function getSourceList(): string
    {
        $cacheKey = $this->provider->identifier();

        try {
            if ($this->cache->has($cacheKey)) {
                return $this->cache->get($cacheKey);
            }

            $this->cache->set(
                key: $cacheKey,
                value: $this->retrieveTopLevelDomainList(),
                ttl: $this->provider->ttl(),
            );

            return $this->cache->get($cacheKey);
        } catch (Throwable $exception) {
            throw DomainParserException::unableToLoadSourceList(
                error: $exception->getMessage(),
                previous: $exception,
            );
        }
    }

    /**
     * @throws DomainParserException
     */
    public function domain(string $domainName): ParsedDomain
    {
        $sourceList = $this->getSourceList();

        try {
            $resolver = $this->provider instanceof IANATopLevelDomainList
                ? TopLevelDomains::fromString($sourceList)
                : Rules::fromString($sourceList);
        } catch (UnableToLoadTopLevelDomainList|UnableToLoadPublicSuffixList $exception) {
            throw DomainParserException::invalidSourceList(
                error: $exception->getMessage(),
                previous: $exception,
            );
        }

        try {
            $domain = Domain::fromIDNA2008($domainName);

            $domainData = $this->provider instanceof IANATopLevelDomainList
                ? $resolver->getIANADomain($domain)
                : $resolver->getICANNDomain($domain);
        } catch (CannotProcessHost|UnableToResolveDomain $exception) {
            throw DomainParserException::unableToParseDomain(
                domainName: $domainName,
                error: $exception->getMessage(),
                previous: $exception,
            );
        }

        $registrableDomain = $domainData
            ->registrableDomain()
            ->toString();
        $tld = $domainData
            ->suffix()
            ->toString();
        $subdomain = $domainData
            ->subDomain()
            ->toString();

        return new ParsedDomain(
            registrableDomain: $registrableDomain,
            tld: $tld,
            subdomain: empty($subdomain)
                ? null
                : $subdomain,
        );
    }
}
