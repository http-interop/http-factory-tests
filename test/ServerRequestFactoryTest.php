<?php

namespace Interop\Http\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactoryTest extends TestCase
{
    /** @var  ServerRequestInterface */
    private $factory;

    public function setUp()
    {
        $factoryClass = SERVER_REQUEST_FACTORY;
        $this->factory = new $factoryClass();
    }

    private function assertServerRequest($request, $method, $uri)
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

        $request = $this->factory->createServerRequest($server);

        $this->assertServerRequest($request, $method, $uri);
    }

    /**
     * @dataProvider dataServer
     */
    public function testCreateServerRequestWithOverridenMethod($server)
    {
        $method = 'OPTIONS';
        $uri = "http://{$server['HTTP_HOST']}{$server['REQUEST_URI']}?{$server['QUERY_STRING']}";

        $request = $this->factory->createServerRequest($server, $method);

        $this->assertServerRequest($request, $method, $uri);
    }

    /**
     * @dataProvider dataServer
     */
    public function testCreateServerRequestWithOverridenUri($server)
    {
        $method = $server['REQUEST_METHOD'];
        $uri = "https://example.com/foobar?bar=2&foo=false";

        $request = $this->factory->createServerRequest($server, null, $uri);

        $this->assertServerRequest($request, $method, $uri);
    }

    /**
     * @dataProvider dataServer
     */
    public function testCreateServerRequestWithUriObject($server)
    {
        $factoryClass = URI_FACTORY;
        $uriFactory = new $factoryClass();

        $method = $server['REQUEST_METHOD'];
        $uri = "http://{$server['HTTP_HOST']}{$server['REQUEST_URI']}?{$server['QUERY_STRING']}";

        $request = $this->factory->createServerRequest(array(), $method, $uriFactory->createUri($uri));

        $this->assertServerRequest($request, $method, $uri);
    }

    public function testCreateServerRequestDoesNotReadServerSuperglobal()
    {
        $_SERVER = ['HTTP_X_FOO' => 'bar'];
        $request = $this->factory->createServerRequest([], 'POST', 'http://example.org/test');
        $this->assertEmpty($request->getServerParams());
    }

    public function testCreateServerRequestDoesNotReadCookieSuperglobal()
    {
        $_COOKIE = ['foo' => 'bar'];
        $request = $this->factory->createServerRequest([], 'POST', 'http://example.org/test');
        $this->assertEmpty($request->getCookieParams());
    }

    public function testCreateServerRequestDoesNotReadGetSuperglobal()
    {
        $_GET = ['foo' => 'bar'];
        $request = $this->factory->createServerRequest([], 'POST', 'http://example.org/test');
        $this->assertEmpty($request->getQueryParams());
    }

    public function testCreateServerRequestDoesNotReadFilesSuperglobal()
    {
        $_FILES = [['name' => 'foobar.dat', 'type' => 'application/octet-stream', 'tmp_name' => '/tmp/php45sd3f', 'error' => UPLOAD_ERR_OK, 'size' => 4]];
        $request = $this->factory->createServerRequest([], 'POST', 'http://example.org/test');
        $this->assertEmpty($request->getUploadedFiles());
    }

    public function testCreateServerRequestDoesNotReadPostSuperglobal()
    {
        $_POST = ['foo' => 'bar'];
        $request = $this->factory->createServerRequest(['CONTENT_TYPE' => 'application/x-www-form-urlencoded'], 'POST', 'http://example.org/test');
        $this->assertEmpty($request->getParsedBody());
    }
}
