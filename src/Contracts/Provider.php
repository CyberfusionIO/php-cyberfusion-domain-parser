<?php

namespace Cyberfusion\DomainParser\Contracts;

interface Provider
{
    public function url(): string;
    public function identifier(): string;
    public function ttl(): int;
}