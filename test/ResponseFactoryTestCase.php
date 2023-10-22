<?php

namespace Interop\Http\Factory;

use PHPUnit\Framework\Attributes\DataProvider;
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
        static::assertInstanceOf(ResponseInterface::class, $response);
        static::assertSame($code, $response->getStatusCode());
    }

    public static function dataCodes()
    {
        return [
            '200' => [200],
            '301' => [301],
            '404' => [404],
            '500' => [500],
        ];
    }

    #[DataProvider('dataCodes')]
    public function testCreateResponse($code)
    {
        $response = $this->factory->createResponse($code);

        $this->assertResponse($response, $code);
    }
}
