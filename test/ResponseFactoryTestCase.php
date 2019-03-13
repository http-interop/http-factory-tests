<?php

namespace Interop\Http\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

abstract class ResponseFactoryTestCase extends TestCase
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $factory;

    /**
     * @return ResponseFactoryInterface
     */
    abstract protected function createResponseFactory();

    public function setUp(): void
    {
        $this->factory = $this->createResponseFactory();
    }

    protected function assertResponse($response, $code)
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
