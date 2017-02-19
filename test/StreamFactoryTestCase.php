<?php

namespace Interop\Http\Factory;

use Interop\Http\Factory\StreamFactoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

abstract class StreamFactoryTestCase extends TestCase
{
    use StreamHelper;

    /**
     * @var StreamFactoryInterface
     */
    protected $factory;

    /**
     * @return StreamFactoryInterface
     */
    abstract protected function createStreamFactory();

    public function setUp()
    {
        $this->factory = $this->createStreamFactory();
    }

    protected function assertStream($stream, $content)
    {
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame($content, (string) $stream);
    }

    public function testCreateStream()
    {
        $string = 'would you like some crumpets?';

        $stream = $this->factory->createStream($string);

        $this->assertStream($stream, $string);
    }

    public function testCreateStreamFromFile()
    {
        $string = 'would you like some crumpets?';
        $filename = $this->createTemporaryFile();

        file_put_contents($filename, $string);

        $stream = $this->factory->createStreamFromFile($filename);

        $this->assertStream($stream, $string);
    }

    public function testCreateStreamFromResource()
    {
        $string = 'would you like some crumpets?';
        $resource = $this->createTemporaryResource($string);

        $stream = $this->factory->createStreamFromResource($resource);

        $this->assertStream($stream, $string);
    }
}
