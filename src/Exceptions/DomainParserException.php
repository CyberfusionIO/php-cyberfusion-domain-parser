<?php

namespace Cyberfusion\DomainParser\Exceptions;

use Exception;
use Throwable;

class DomainParserException extends Exception
{
    public static function unableToLoadSuffixList(string $error, Throwable $previous = null): self
    {
        return new self(
            message: sprintf('Unable to load the suffix list, error: `%s`', $error),
            previous: $previous,
        );
    }

    public static function invalidSuffixList(string $error, Throwable $previous = null): self
    {
        return new self(
            message: sprintf('The provided suffix list is invalid, error: `%s`', $error),
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