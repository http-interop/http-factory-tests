<?php
/**
 * @author       http-factory-tests Contributors
 * @license      MIT
 * @link         https://github.com/http-interop/http-factory-tests
 *
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use function class_exists;
use function defined;
use function fclose;
use function file_exists;
use function file_put_contents;
use function fopen;
use function fseek;
use function ftell;
use function fwrite;
use function rewind;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;
use const SEEK_END;
use const SEEK_SET;

class StreamFactoryTestCase extends TestCase
{

    protected StreamFactoryInterface $streamFactory;
    protected static array           $tempFiles = [];

    public function setUp(): void
    {
        $this->streamFactory = $this->createStreamFactory();
    }

    protected function createStreamFactory(): StreamFactoryInterface
    {
        if (!defined('STREAM_FACTORY') || !class_exists(STREAM_FACTORY)) {
            static::markTestSkipped('STREAM_FACTORY class name not provided');
        }

        return new (STREAM_FACTORY);
    }

    public static function tearDownAfterClass(): void
    {
        foreach(static::$tempFiles as $tempFile){
            if(file_exists($tempFile)){
                unlink($tempFile);
            }
        }
    }

    protected function createTemporaryFile(): string
    {
        $file = tempnam(sys_get_temp_dir(), 'http_factory_tests_');

        if($file === false){
            throw new RuntimeException('could not create temp file');
        }

        static::$tempFiles[] = $file;

        return $file;
    }

    /**
     * @return resource
     */
    protected function createTemporaryResource(string $content = null)
    {
        $file     = $this->createTemporaryFile();
        $resource = fopen($file, 'r+');

        if($content){
            fwrite($resource, $content);
            rewind($resource);
        }

        return $resource;
    }

    public function testCreateStreamWithoutArgument(): void
    {
        $stream = $this->streamFactory->createStream();

        static::assertSame('', (string) $stream);
    }

    public function testCreateStreamWithEmptyString(): void
    {
        $string = '';

        $stream = $this->streamFactory->createStream($string);

        static::assertSame($string, (string) $stream);
    }

    public function testCreateStreamWithASCIIString(): void
    {
        $string = 'would you like some crumpets?';

        $stream = $this->streamFactory->createStream($string);

        static::assertSame($string, (string) $stream);
    }

    public function testCreateStreamWithMultiByteMultiLineString(): void
    {
        $string = "would you\r\nlike some\n\u{1F950}?";

        $stream = $this->streamFactory->createStream($string);

        static::assertSame($string, (string) $stream);
    }

    /**
     * @noinspection PhpUnreachableStatementInspection, PhpUnitTestFailedLineInspection
     */
    public function testCreateStreamCursorPosition(): void
    {
        $this->markTestIncomplete('This behaviour has not been specified by PHP-FIG yet.');

        $string = 'would you like some crumpets?';

        $stream = $this->streamFactory->createStream($string);

        static::assertSame(29, $stream->tell());
    }

    public function testCreateStreamFromFile(): void
    {
        $string   = 'would you like some crumpets?';
        $filename = $this->createTemporaryFile();

        file_put_contents($filename, $string);

        $stream = $this->streamFactory->createStreamFromFile($filename);

        static::assertSame($string, (string) $stream);
    }

    public function testCreateStreamFromNonExistingFile(): void
    {
        $filename = $this->createTemporaryFile();

        unlink($filename);

        $this->expectException(RuntimeException::class);
        $this->streamFactory->createStreamFromFile($filename);
    }

    public function testCreateStreamFromInvalidFileName(): void
    {
        $this->expectException(RuntimeException::class);
        $this->streamFactory->createStreamFromFile('');
    }

    public function testCreateStreamFromFileIsReadOnlyByDefault(): void
    {
        $string   = 'would you like some crumpets?';
        $filename = $this->createTemporaryFile();

        $stream = $this->streamFactory->createStreamFromFile($filename);

        $this->expectException(RuntimeException::class);
        $stream->write($string);
    }

    public function testCreateStreamFromFileWithWriteOnlyMode(): void
    {
        $filename = $this->createTemporaryFile();

        $stream = $this->streamFactory->createStreamFromFile($filename, 'w');

        $this->expectException(RuntimeException::class);
        $stream->read(1);
    }

    public function testCreateStreamFromFileWithNoMode(): void
    {
        $filename = $this->createTemporaryFile();

        $this->expectException(Exception::class);
        $this->streamFactory->createStreamFromFile($filename, '');
    }

    public function testCreateStreamFromFileWithInvalidMode(): void
    {
        $filename = $this->createTemporaryFile();

        $this->expectException(Exception::class);
        $this->streamFactory->createStreamFromFile($filename, "\u{2620}");
    }

    public function testCreateStreamFromFileCursorPosition(): void
    {
        $string   = 'would you like some crumpets?';
        $filename = $this->createTemporaryFile();

        file_put_contents($filename, $string);

        $resource  = fopen($filename, 'r');
        $fopenTell = ftell($resource);

        fclose($resource);

        $stream = $this->streamFactory->createStreamFromFile($filename);

        static::assertSame($fopenTell, $stream->tell());
    }

    public function testCreateStreamFromResource(): void
    {
        $string   = 'would you like some crumpets?';
        $resource = $this->createTemporaryResource($string);

        $stream = $this->streamFactory->createStreamFromResource($resource);

        static::assertSame($string, (string) $stream);
    }

    public static function cursorPositionProvider():array
    {
        $string = 'would you like some crumpets?';

        return [
            'SEEK_SET, 0'  => [$string,  0, SEEK_SET,  0],
            'SEEK_END, 0'  => [$string,  0, SEEK_END, 29],
            'SEEK_SET, 15' => [$string, 15, SEEK_SET, 15],
        ];
    }

    #[DataProvider('cursorPositionProvider')]
    public function testCreateStreamFromResourceCursorPosition(string $string, int $offset, int $whence, int $expected): void
    {
        $resource = $this->createTemporaryResource($string);

        fseek($resource, $offset, $whence);

        $stream = $this->streamFactory->createStreamFromResource($resource);

        static::assertSame($expected, $stream->tell());
    }

}
