<?php

namespace Interop\Http\Factory;

use Interop\Http\Factory\UriFactoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

abstract class UriFactoryTestCase extends TestCase
{
    /**
     * @var UriFactoryInterface
     */
    protected $factory;

    /**
     * @return UriFactoryInterface
     */
    abstract protected function createUriFactory();

    public function setUp()
    {
        $this->factory = $this->createUriFactory();
    }

    protected function assertUri($uri, $uriString)
    {
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertSame($uriString, (string) $uri);
    }

    public function testCreateUri()
    {
        $uriString = 'http://example.com/';

        $uri = $this->factory->createUri($uriString);

        $this->assertUri($uri, $uriString);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenUriIsInvalid()
    {
        $this->factory->createUri(':');
    }
}
