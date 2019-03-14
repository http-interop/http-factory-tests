<?php

namespace Interop\Http\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

abstract class ServerRequestFactoryTestCase extends TestCase
{
    /**
     * @var ServerRequestFactoryInterface
     */
    protected $factory;

    /**
     * @return ServerRequestFactoryInterface
     */
    abstract protected function createServerRequestFactory();

    /**
     * @param string $uri
     *
     * @return UriInterface
     */
    abstract protected function createUri($uri);

    public function setUp(): void
    {
        $this->factory = $this->createServerRequestFactory();
    }

    protected function assertServerRequest($request, $method, $uri)
    {
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertSame($method, $request->getMethod());
        $this->assertSame($uri, (string) $request->getUri());
    }

    public function dataMethods()
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['DELETE'],
            ['OPTIONS'],
            ['HEAD'],
        ];
    }

    public function dataServer()
    {
        $data = [];

        foreach ($this->dataMethods() as $methodData) {
            $data[] = [
                [
                    'REQUEST_METHOD' => $methodData[0],
                    'REQUEST_URI' => '/test',
                    'QUERY_STRING' => 'foo=1&bar=true',
                    'HTTP_HOST' => 'example.org',
                ]
            ];
        }

        return $data;
    }

    /**
     * @dataProvider dataServer
     */
    public function testCreateServerRequest($server)
    {
        $method = $server['REQUEST_METHOD'];
        $uri = "http://{$server['HTTP_HOST']}{$server['REQUEST_URI']}?{$server['QUERY_STRING']}";

        $request = $this->factory->createServerRequest($method, $uri);

        $this->assertServerRequest($request, $method, $uri);
    }

    /**
     * @dataProvider dataServer
     */
    public function testCreateServerRequestFromArray(array $server)
    {
        $method = $server['REQUEST_METHOD'];
        $uri = "http://{$server['HTTP_HOST']}{$server['REQUEST_URI']}?{$server['QUERY_STRING']}";

        $request = $this->factory->createServerRequest($method, $uri, $server);

        $this->assertServerRequest($request, $method, $uri);
    }

    /**
     * @dataProvider dataServer
     */
    public function testCreateServerRequestWithUriObject($server)
    {
        $method = $server['REQUEST_METHOD'];
        $uri = "http://{$server['HTTP_HOST']}{$server['REQUEST_URI']}?{$server['QUERY_STRING']}";

        $request = $this->factory->createServerRequest($method, $this->createUri($uri));

        $this->assertServerRequest($request, $method, $uri);
    }

    /**
     * @backupGlobals enabled
     */
    public function testCreateServerRequestDoesNotReadServerSuperglobal()
    {
        $_SERVER = ['HTTP_X_FOO' => 'bar'];

        $server = [
            'REQUEST_METHOD' => 'PUT',
            'REQUEST_URI' => '/test',
            'QUERY_STRING' => 'super=0',
            'HTTP_HOST' => 'example.org',
        ];

        $request = $this->factory->createServerRequest('PUT', '/test', $server);

        $serverParams = $request->getServerParams();

        $this->assertNotEquals($_SERVER, $serverParams);
        $this->assertArrayNotHasKey('HTTP_X_FOO', $serverParams);
    }

    public function testCreateServerRequestDoesNotReadCookieSuperglobal()
    {
        $_COOKIE = ['foo' => 'bar'];

        $request = $this->factory->createServerRequest('POST', 'http://example.org/test');

        $this->assertEmpty($request->getCookieParams());
    }

    public function testCreateServerRequestDoesNotReadGetSuperglobal()
    {
        $_GET = ['foo' => 'bar'];

        $request = $this->factory->createServerRequest('POST', 'http://example.org/test');

        $this->assertEmpty($request->getQueryParams());
    }

    public function testCreateServerRequestDoesNotReadFilesSuperglobal()
    {
        $_FILES = [['name' => 'foobar.dat', 'type' => 'application/octet-stream', 'tmp_name' => '/tmp/php45sd3f', 'error' => UPLOAD_ERR_OK, 'size' => 4]];

        $request = $this->factory->createServerRequest('POST', 'http://example.org/test');

        $this->assertEmpty($request->getUploadedFiles());
    }

    public function testCreateServerRequestDoesNotReadPostSuperglobal()
    {
        $_POST = ['foo' => 'bar'];

        $request = $this->factory->createServerRequest('POST', 'http://example.org/test');

        $this->assertEmpty($request->getParsedBody());
    }
}
