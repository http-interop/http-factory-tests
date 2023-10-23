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
use Psr\Http\Message\ResponseFactoryInterface;
use function class_exists;
use function defined;

class ResponseFactoryTest extends TestCase
{

    protected ResponseFactoryInterface $responseFactory;

    public function setUp(): void
    {
        $this->responseFactory = $this->createResponseFactory();
    }

    protected function createResponseFactory(): ResponseFactoryInterface
    {
        if(!defined('RESPONSE_FACTORY') || !class_exists(RESPONSE_FACTORY)){
            static::markTestSkipped('RESPONSE_FACTORY class name not provided');
        }

        return new (RESPONSE_FACTORY);
    }

    public static function dataCodes(): array
    {
        return [
            'HTTP/200' => [200],
            'HTTP/301' => [301],
            'HTTP/404' => [404],
            'HTTP/500' => [500],
        ];
    }

    #[DataProvider('dataCodes')]
    public function testCreateResponse(int $code): void
    {
        $response = $this->responseFactory->createResponse($code);

        static::assertSame($code, $response->getStatusCode());
    }

}
