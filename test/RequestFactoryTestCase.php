<?php

namespace Interop\Http\Factory;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

abstract class RequestFactoryTestCase extends TestCase
{
    /**
     * @var RequestFactoryInterface
     */
    protected $factory;

    /**
     * @return RequestFactoryInterface
     */
    abstract protected function createRequestFactory();

    /**
     * @param string $uri
     *
     * @return UriInterface
     */
    abstract protected function createUri($uri);

    public function setUp(): void
    {
        $this->factory = $this->createRequestFactory();
    }

    protected function assertRequest($request, $method, $uri)
    {
        static::assertInstanceOf(RequestInterface::class, $request);
        static::assertSame($method, $request->getMethod());
        static::assertSame($uri, (string) $request->getUri());
    }

    public static function dataMethods()
    {
        return [
            'GET'     => ['GET'],
            'POST'    => ['POST'],
            'PUT'     => ['PUT'],
            'DELETE'  => ['DELETE'],
            'OPTIONS' => ['OPTIONS'],
            'HEAD'    => ['HEAD'],
        ];
    }

    #[DataProvider('dataMethods')]
    public function testCreateRequest($method)
    {
        $uri = 'http://example.com/';

        $request = $this->factory->createRequest($method, $uri);

        $this->assertRequest($request, $method, $uri);
    }

    public function testCreateRequestWithUri()
    {
        $method = 'GET';
        $uri = 'http://example.com/';

        $request = $this->factory->createRequest($method, $this->createUri($uri));

        $this->assertRequest($request, $method, $uri);
    }
}
