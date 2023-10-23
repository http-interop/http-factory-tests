<?php
/**
 * @author       http-factory-tests Contributors
 * @license      MIT
 * @link         https://github.com/http-interop/http-factory-tests
 *
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriFactoryInterface;
use function class_exists;
use function defined;

class UriFactoryTestCase extends TestCase
{

    protected UriFactoryInterface $uriFactory;

    public function setUp(): void
    {
        $this->uriFactory = $this->createUriFactory();
    }

    protected function createUriFactory(): UriFactoryInterface
    {
        if(!defined('URI_FACTORY') || !class_exists(URI_FACTORY)){
            static::markTestSkipped('URI_FACTORY class name not provided');
        }

        return new (URI_FACTORY);
    }

    public function testCreateUri(): void
    {
        $uriString = 'https://example.com/';
        $uri       = $this->uriFactory->createUri($uriString);

        static::assertSame($uriString, (string) $uri);
    }

    public function testExceptionWhenUriIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->uriFactory->createUri(':');
    }

}
