<?php

namespace Cyberfusion\DomainParser\Tests;

use Cyberfusion\DomainParser\Data\ParsedDomain;
use PHPUnit\Framework\TestCase;

class ParsedDomainTest extends TestCase
{
    public function testWithoutSubdomain(): void
    {
        $parsedDomain = new ParsedDomain('cyberfusion.nl', 'nl');

        $this->assertSame('cyberfusion.nl', $parsedDomain->getRegistrableDomain());
        $this->assertSame('cyberfusion', $parsedDomain->getSld());
        $this->assertSame('nl', $parsedDomain->getTld());
        $this->assertNull($parsedDomain->getSubdomain());
        $this->assertFalse($parsedDomain->hasSubdomain());
        $this->assertTrue($parsedDomain->isApexDomain());
        $this->assertSame('cyberfusion.nl', $parsedDomain->getFqdn());
    }

    public function testWithSubdomain(): void
    {
        $parsedDomain = new ParsedDomain('cyberfusion.nl', 'nl', 'cluster.lord');

        $this->assertSame('cyberfusion.nl', $parsedDomain->getRegistrableDomain());
        $this->assertSame('cyberfusion', $parsedDomain->getSld());
        $this->assertSame('nl', $parsedDomain->getTld());
        $this->assertSame('cluster.lord', $parsedDomain->getSubdomain());
        $this->assertTrue($parsedDomain->hasSubdomain());
        $this->assertFalse($parsedDomain->isApexDomain());
        $this->assertSame('cluster.lord.cyberfusion.nl', $parsedDomain->getFqdn());
    }
}