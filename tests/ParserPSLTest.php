<?php

namespace Cyberfusion\DomainParser\Tests;

use Cyberfusion\DomainParser\Exceptions\DomainParserException;
use Cyberfusion\DomainParser\Parser;
use Cyberfusion\DomainParser\Providers\PublicSuffixList;
use PHPUnit\Framework\TestCase;

class ParserPSLTest extends TestCase
{
    private Parser $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new Parser(provider: new PublicSuffixList());
    }

    public function testParserWithoutSubdomain(): void
    {
        $parsedDomain = $this
            ->parser
            ->domain('cyberfusion.nl');

        $this->assertSame('cyberfusion.nl', $parsedDomain->getRegistrableDomain());
        $this->assertSame('cyberfusion', $parsedDomain->getSld());
        $this->assertSame('nl', $parsedDomain->getTld());
        $this->assertSame('cyberfusion.nl', $parsedDomain->getFqdn());
        $this->assertNull($parsedDomain->getSubdomain());
        $this->assertFalse($parsedDomain->hasSubdomain());
    }

    public function testParserWithoutSubdomainWithDoubleTLD(): void
    {
        $parsedDomain = $this
            ->parser
            ->domain('cyberfusion.co.uk');

        $this->assertSame('cyberfusion.co.uk', $parsedDomain->getRegistrableDomain());
        $this->assertSame('cyberfusion', $parsedDomain->getSld());
        $this->assertSame('co.uk', $parsedDomain->getTld());
        $this->assertSame('cyberfusion.co.uk', $parsedDomain->getFqdn());
        $this->assertNull($parsedDomain->getSubdomain());
        $this->assertFalse($parsedDomain->hasSubdomain());
    }

    public function testParserWithSubdomain(): void
    {
        $parsedDomain = $this
            ->parser
            ->domain('cluster.lord.cyberfusion.nl');

        $this->assertSame('cyberfusion.nl', $parsedDomain->getRegistrableDomain());
        $this->assertSame('cyberfusion', $parsedDomain->getSld());
        $this->assertSame('nl', $parsedDomain->getTld());
        $this->assertSame('cluster.lord.cyberfusion.nl', $parsedDomain->getFqdn());
        $this->assertSame('cluster.lord', $parsedDomain->getSubdomain());
        $this->assertTrue($parsedDomain->hasSubdomain());
    }

    public function testParserWithSubdomainWithDoubleTLD(): void
    {
        $parsedDomain = $this
            ->parser
            ->domain('cluster.lord.cyberfusion.co.uk');

        $this->assertSame('cyberfusion.co.uk', $parsedDomain->getRegistrableDomain());
        $this->assertSame('cyberfusion', $parsedDomain->getSld());
        $this->assertSame('co.uk', $parsedDomain->getTld());
        $this->assertSame('cluster.lord.cyberfusion.co.uk', $parsedDomain->getFqdn());
        $this->assertSame('cluster.lord', $parsedDomain->getSubdomain());
        $this->assertTrue($parsedDomain->hasSubdomain());
    }

    public function testParserWithInvalidDomain(): void
    {
        $this->expectException(DomainParserException::class);

        $this
            ->parser
            ->domain('dit!isdus+/-geendomein');
    }
}
