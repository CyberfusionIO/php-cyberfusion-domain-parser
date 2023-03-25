<?php

namespace Cyberfusion\DomainParser\Tests;

use Cyberfusion\DomainParser\Exceptions\DomainParserException;
use Cyberfusion\DomainParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private Parser $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->parser = new Parser();
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
    }

    public function testParserWithInvalidDomain(): void
    {
        $this->expectException(DomainParserException::class);

        $this
            ->parser
            ->domain('dit!isdus+/-geendomein');
    }
}