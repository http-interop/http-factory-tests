<?php

namespace Interop\Http\Factory;

use Exception;
use RuntimeException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use function fclose;
use function file_put_contents;
use function fopen;
use function fseek;
use function ftell;
use function strlen;
use function unlink;
use const SEEK_END;
use const SEEK_SET;

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

    public function setUp(): void
    {
        $this->factory = $this->createStreamFactory();
    }

    protected function assertStream($stream, $content)
    {
        static::assertInstanceOf(StreamInterface::class, $stream);
        static::assertSame($content, (string) $stream);
    }

    public function testCreateStreamWithoutArgument()
    {
        $stream = $this->factory->createStream();

        $this->assertStream($stream, '');
    }

    public function testCreateStreamWithEmptyString()
    {
        $string = '';

        $stream = $this->factory->createStream($string);

        $this->assertStream($stream, $string);
    }

    public function testCreateStreamWithASCIIString()
    {
        $string = 'would you like some crumpets?';

        $stream = $this->factory->createStream($string);

        $this->assertStream($stream, $string);
    }

    public function testCreateStreamWithMultiByteMultiLineString()
    {
        $string = "would you\r\nlike some\n\u{1F950}?";

        $stream = $this->factory->createStream($string);

        $this->assertStream($stream, $string);
    }

    /**
     * @noinspection PhpUnreachableStatementInspection
     */
    public function testCreateStreamCursorPosition()
    {
        $this->markTestIncomplete('This behaviour has not been specified by PHP-FIG yet.');

        $string = 'would you like some crumpets?';

        $stream = $this->factory->createStream($string);

        static::assertSame(strlen($string), $stream->tell());
    }

    public function testCreateStreamFromFile()
    {
        $string = 'would you like some crumpets?';
        $filename = $this->createTemporaryFile();

        file_put_contents($filename, $string);

        $stream = $this->factory->createStreamFromFile($filename);

        $this->assertStream($stream, $string);
    }

    public function testCreateStreamFromNonExistingFile()
    {
        $filename = $this->createTemporaryFile();
        unlink($filename);

        $this->expectException(RuntimeException::class);
        $this->factory->createStreamFromFile($filename);
    }

    public function testCreateStreamFromInvalidFileName()
    {
        $this->expectException(RuntimeException::class);
        $this->factory->createStreamFromFile('');
    }

    public function testCreateStreamFromFileIsReadOnlyByDefault()
    {
        $string = 'would you like some crumpets?';
        $filename = $this->createTemporaryFile();

        $stream = $this->factory->createStreamFromFile($filename);

        $this->expectException(RuntimeException::class);
        $stream->write($string);
    }

    public function testCreateStreamFromFileWithWriteOnlyMode()
    {
        $filename = $this->createTemporaryFile();

        $stream = $this->factory->createStreamFromFile($filename, 'w');

        $this->expectException(RuntimeException::class);
        $stream->read(1);
    }

    public function testCreateStreamFromFileWithNoMode()
    {
        $filename = $this->createTemporaryFile();

        $this->expectException(Exception::class);
        $this->factory->createStreamFromFile($filename, '');
    }

    public function testCreateStreamFromFileWithInvalidMode()
    {
        $filename = $this->createTemporaryFile();

        $this->expectException(Exception::class);
        $this->factory->createStreamFromFile($filename, "\u{2620}");
    }

    public function testCreateStreamFromFileCursorPosition()
    {
        $string = 'would you like some crumpets?';
        $filename = $this->createTemporaryFile();

        file_put_contents($filename, $string);

        $resource = fopen($filename, 'r');
        $fopenTell = ftell($resource);
        fclose($resource);

        $stream = $this->factory->createStreamFromFile($filename);

        static::assertSame($fopenTell, $stream->tell());
    }

    public function testCreateStreamFromResource()
    {
        $string = 'would you like some crumpets?';
        $resource = $this->createTemporaryResource($string);

        $stream = $this->factory->createStreamFromResource($resource);

        $this->assertStream($stream, $string);
    }

    public function testCreateStreamFromResourceCursorPosition()
    {
        $string = 'would you like some crumpets?';

        $resource1 = $this->createTemporaryResource($string);
        fseek($resource1, 0, SEEK_SET);
        $stream1 = $this->factory->createStreamFromResource($resource1);
        static::assertSame(0, $stream1->tell());

        $resource2 = $this->createTemporaryResource($string);
        fseek($resource2, 0, SEEK_END);
        $stream2 = $this->factory->createStreamFromResource($resource2);
        static::assertSame(strlen($string), $stream2->tell());

        $resource3 = $this->createTemporaryResource($string);
        fseek($resource3, 15, SEEK_SET);
        $stream3 = $this->factory->createStreamFromResource($resource3);
        static::assertSame(15, $stream3->tell());
    }
}
