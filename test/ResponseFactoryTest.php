<?php

namespace Interop\Http\Factory;

use Interop\Http\Factory\ResponseFactoryInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseFactoryTest extends TestCase
{
    /** @var  ResponseFactoryInterface */
    private $factory;

    public function setUp()
    {
        $factoryClass = RESPONSE_FACTORY;
        $this->factory = new $factoryClass();
    }

    private function assertResponse($response, $code)
    {
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame($code, $response->getStatusCode());
    }

    public function dataCodes()
    {
        return [
            [200],
            [301],
            [404],
            [500],
        ];
    }

    /**
     * @dataProvider dataCodes
     */
    public function testCreateResponse($code)
    {
        $response = $this->factory->createResponse($code);

        $this->assertResponse($response, $code);
    }
}
