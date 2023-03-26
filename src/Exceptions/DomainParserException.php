<?php

namespace Cyberfusion\DomainParser\Exceptions;

use Exception;
use Throwable;

class DomainParserException extends Exception
{
    public static function unableToLoadTopLevelDomainList(string $error, Throwable $previous = null): self
    {
        return new self(
            message: sprintf('Unable to load the top level domain list, error: `%s`', $error),
            previous: $previous,
        );
    }

    public static function invalidTopLevelDomainList(string $error, Throwable $previous = null): self
    {
        return new self(
            message: sprintf('The provided top level domain list is invalid, error: `%s`', $error),
            previous: $previous,
        );
    }

    public static function unableToParseDomain(string $domainName, string $error, Throwable $previous = null): self
    {
        return new self(
            message: sprintf('Unable to parse `%s`, error: %s', $domainName, $error),
            previous: $previous,
        );
    }
}
