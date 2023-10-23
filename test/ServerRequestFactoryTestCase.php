<?php
/**
 * @author       http-factory-tests Contributors
 * @license      MIT
 * @link         https://github.com/http-interop/http-factory-tests
 *
 * @noinspection PhpUndefinedConstantInspection, PhpArrayWriteIsNotUsedInspection
 */

namespace Interop\Http\Factory;

use Generator;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use function class_exists;
use function defined;
use const UPLOAD_ERR_OK;

class ServerRequestFactoryTestCase extends TestCase
{

    protected ServerRequestFactoryInterface $serverRequestFactory;
    protected UriFactoryInterface           $uriFactory;

    public function setUp(): void
    {
        $this->serverRequestFactory = $this->createServerRequestFactory();
        $this->uriFactory           = $this->createUriFactory();
    }

    protected function createServerRequestFactory(): ServerRequestFactoryInterface
    {
        if(!defined('SERVER_REQUEST_FACTORY') || !class_exists(SERVER_REQUEST_FACTORY)){
            static::markTestSkipped('SERVER_REQUEST_FACTORY class name not provided');
        }

        return new (SERVER_REQUEST_FACTORY);
    }

    protected function createUriFactory(): UriFactoryInterface
    {
        if(!defined('URI_FACTORY') || !class_exists(URI_FACTORY)){
            static::markTestSkipped('URI_FACTORY class name not provided');
        }

        return new (URI_FACTORY);
    }

    protected static function httpMethods(): array
    {
        return [
            'GET',
            'POST',
            'PATCH',
            'PUT',
            'DELETE',
            'OPTIONS',
            'HEAD',
        ];
    }

    public static function dataServer(): Generator
    {
        foreach(static::httpMethods() as $method){
            yield $method => [$method, 'https://example.org/test?foo=1&bar=true', 'example.org', '/test', 'foo=1&bar=true'];
        }
    }

    #[DataProvider('dataServer')]
    public function testCreateServerRequest(string $method, string $uri): void
    {
        $request = $this->serverRequestFactory->createServerRequest($method, $uri);

        static::assertSame($method, $request->getMethod());
        static::assertSame($uri, (string) $request->getUri());
    }

    #[DataProvider('dataServer')]
    public function testCreateServerRequestFromArray(string $method, string $uri, string $host, string $path, string $query): void
    {
        $server = [
            'REQUEST_METHOD' => $method,
            'HTTP_HOST'      => $host,
            'REQUEST_URI'    => $path,
            'QUERY_STRING'   => $query,
        ];

        $request = $this->serverRequestFactory->createServerRequest($method, $uri, $server);

        static::assertSame($method, $request->getMethod());
        static::assertSame($uri, (string) $request->getUri());
    }

    #[DataProvider('dataServer')]
    public function testCreateServerRequestWithUriObject(string $method, string $uri): void
    {
        $request = $this->serverRequestFactory->createServerRequest($method, $this->uriFactory->createUri($uri));

        static::assertSame($method, $request->getMethod());
        static::assertSame($uri, (string) $request->getUri());
    }

    #[BackupGlobals(true)]
    public function testCreateServerRequestDoesNotReadServerSuperglobal(): void
    {
        $_SERVER = ['HTTP_X_FOO' => 'bar'];

        $server = [
            'REQUEST_METHOD' => 'PUT',
            'REQUEST_URI'    => '/test',
            'QUERY_STRING'   => 'super=0',
            'HTTP_HOST'      => 'example.org',
        ];

        $request      = $this->serverRequestFactory->createServerRequest('PUT', '/test', $server);
        $serverParams = $request->getServerParams();

        static::assertNotEquals($_SERVER, $serverParams);
        static::assertArrayNotHasKey('HTTP_X_FOO', $serverParams);
    }

    public function testCreateServerRequestDoesNotReadCookieSuperglobal(): void
    {
        $_COOKIE = ['foo' => 'bar'];

        $request = $this->serverRequestFactory->createServerRequest('POST', 'https://example.org/test');

        static::assertEmpty($request->getCookieParams());
    }

    public function testCreateServerRequestDoesNotReadGetSuperglobal(): void
    {
        $_GET = ['foo' => 'bar'];

        $request = $this->serverRequestFactory->createServerRequest('POST', 'https://example.org/test');

        static::assertEmpty($request->getQueryParams());
    }

    public function testCreateServerRequestDoesNotReadFilesSuperglobal(): void
    {
        $_FILES = [
            [
                'name'     => 'foobar.dat',
                'type'     => 'application/octet-stream',
                'tmp_name' => '/tmp/php45sd3f',
                'error'    => UPLOAD_ERR_OK,
                'size'     => 4,
            ],
        ];

        $request = $this->serverRequestFactory->createServerRequest('POST', 'https://example.org/test');

        static::assertEmpty($request->getUploadedFiles());
    }

    public function testCreateServerRequestDoesNotReadPostSuperglobal(): void
    {
        $_POST = ['foo' => 'bar'];

        $request = $this->serverRequestFactory->createServerRequest('POST', 'https://example.org/test');

        static::assertEmpty($request->getParsedBody());
    }

}
