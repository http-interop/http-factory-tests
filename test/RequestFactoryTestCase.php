<?php
/**
 * @author       http-factory-tests Contributors
 * @license      MIT
 * @link         https://github.com/http-interop/http-factory-tests
 *
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use function class_exists;
use function defined;

class RequestFactoryTestCase extends TestCase
{

    protected RequestFactoryInterface $requestFactory;
    protected UriFactoryInterface     $uriFactory;

    public function setUp(): void
    {
        $this->requestFactory = $this->createRequestFactory();
        $this->uriFactory     = $this->createUriFactory();
    }

    protected function createRequestFactory(): RequestFactoryInterface
    {
        if(!defined('REQUEST_FACTORY') || !class_exists(REQUEST_FACTORY)){
            static::markTestSkipped('REQUEST_FACTORY class name not provided');
        }

        return new (REQUEST_FACTORY);
    }

    protected function createUriFactory(): UriFactoryInterface
    {
        if(!defined('URI_FACTORY') || !class_exists(URI_FACTORY)){
            static::markTestSkipped('URI_FACTORY class name not provided');
        }

        return new (URI_FACTORY);
    }

    public static function dataMethods(): array
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
    public function testCreateRequest(string $method): void
    {
        $uri     = 'https://example.com/';
        $request = $this->requestFactory->createRequest($method, $uri);

        static::assertSame($method, $request->getMethod());
        static::assertSame($uri, (string) $request->getUri());
    }

    public function testCreateRequestWithUri(): void
    {
        $method  = 'GET';
        $uri     = 'https://example.com/';
        $request = $this->requestFactory->createRequest($method, $this->uriFactory->createUri($uri));

        static::assertSame($method, $request->getMethod());
        static::assertSame($uri, (string) $request->getUri());
    }

}
