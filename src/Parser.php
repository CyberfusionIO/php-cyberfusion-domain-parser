<?php

namespace Cyberfusion\DomainParser;

use Cyberfusion\DomainParser\Data\ParsedDomain;
use Cyberfusion\DomainParser\Exceptions\DomainParserException;
use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\File;
use GuzzleHttp\Client;
use Pdp\CannotProcessHost;
use Pdp\Domain;
use Pdp\TopLevelDomains;
use Pdp\UnableToLoadTopLevelDomainList;
use Psr\SimpleCache\CacheInterface;
use Throwable;

class Parser
{
    private const IANA_LIST_SOURCE = 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt';

    private const IANA_CACHE_KEY = 'iana';

    private const IANA_CACHE_TTL = 86400; // Daily

    private File|AdapterInterface|CacheInterface $cache;

    public function __construct(CacheInterface|AdapterInterface|null $cache = null)
    {
        $this->cache = $cache ?? new File(__DIR__);
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
            $response = $client->get(self::IANA_LIST_SOURCE);
        } catch (Throwable $exception) {
            throw DomainParserException::unableToLoadTopLevelDomainList(
                error: $exception->getMessage(),
                previous: $exception,
            );
        }

        return (string) $response->getBody();
    }

    /**
     * @throws DomainParserException
     */
    private function getTopLevelDomainList(): string
    {
        try {
            if ($this->cache->has(self::IANA_CACHE_KEY)) {
                return $this->cache->get(self::IANA_CACHE_KEY);
            }

            $this->cache->set(
                key: self::IANA_CACHE_KEY,
                value: $this->retrieveTopLevelDomainList(),
                ttl: self::IANA_CACHE_TTL,
            );

            return $this->cache->get(self::IANA_CACHE_KEY);
        } catch (Throwable $exception) {
            throw DomainParserException::unableToLoadTopLevelDomainList(
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
        $publicSuffixList = $this->getTopLevelDomainList();

        try {
            $topLevelDomains = TopLevelDomains::fromString($publicSuffixList);
        } catch (UnableToLoadTopLevelDomainList $exception) {
            throw DomainParserException::invalidTopLevelDomainList(
                error: $exception->getMessage(),
                previous: $exception
            );
        }

        try {
            $domainData = $topLevelDomains->resolve(Domain::fromIDNA2008($domainName));
        } catch (CannotProcessHost $exception) {
            throw DomainParserException::unableToParseDomain(
                domainName: $domainName,
                error: $exception->getMessage(),
                previous: $exception
            );
        }

        return new ParsedDomain(
            registrableDomain: $domainData
                ->registrableDomain()
                ->toString(),
            tld: $domainData
                ->suffix()
                ->toString(),
            subdomain: $domainData
                ->subDomain()
                ->toString()
        );
    }
}
