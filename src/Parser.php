<?php

namespace Cyberfusion\DomainParser;

use Cyberfusion\DomainParser\Data\ParsedDomain;
use Cyberfusion\DomainParser\Exceptions\DomainParserException;
use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\File;
use GuzzleHttp\Client;
use Pdp\CannotProcessHost;
use Pdp\Domain;
use Pdp\Rules;
use Pdp\UnableToLoadPublicSuffixList;
use Psr\SimpleCache\CacheInterface;
use Throwable;

class Parser
{
    private const PUBLIC_SUFFIX_LIST_SOURCE = 'https://publicsuffix.org/list/public_suffix_list.dat';
    private const PUBLIC_SUFFIX_LIST_CACHE_KEY = 'public-suffix-list';
    private const PUBLIC_SUFFIX_LIST_CACHE_TTL = 7 * 86400;

    private File|AdapterInterface|CacheInterface $cache;

    public function __construct(CacheInterface|AdapterInterface|null $cache = null)
    {
        $this->cache = $cache ?? new File(__DIR__);
    }

    /**
     * @throws DomainParserException
     */
    private function retrieveSuffixListContent(): string
    {
        $client = new Client([
            'connect_timeout' => 10,
            'timeout' => 30,
        ]);
        try {
            $response = $client->get(self::PUBLIC_SUFFIX_LIST_SOURCE);
        } catch (Throwable $exception) {
            throw DomainParserException::unableToLoadSuffixList(
                error: $exception->getMessage(),
                previous: $exception,
            );
        }

        return (string)$response->getBody();
    }

    /**
     * @throws DomainParserException
     */
    private function getSuffixListContent(): string
    {
        try {
            if ($this->cache->has(self::PUBLIC_SUFFIX_LIST_CACHE_KEY)) {
                return $this->cache->get(self::PUBLIC_SUFFIX_LIST_CACHE_KEY);
            }

            $this->cache->set(
                key: self::PUBLIC_SUFFIX_LIST_CACHE_KEY,
                value: $this->retrieveSuffixListContent(),
                ttl: self::PUBLIC_SUFFIX_LIST_CACHE_TTL,
            );

            return $this->cache->get(self::PUBLIC_SUFFIX_LIST_CACHE_KEY);
        } catch (Throwable $exception) {
            throw DomainParserException::unableToLoadSuffixList(
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
        $publicSuffixList = $this->getSuffixListContent();

        try {
            $rules = Rules::fromString($publicSuffixList);
        } catch (UnableToLoadPublicSuffixList $exception) {
            throw DomainParserException::invalidSuffixList(
                error: $exception->getMessage(),
                previous: $exception
            );
        }

        try {
            $domainData = $rules->resolve(Domain::fromIDNA2008($domainName));
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
