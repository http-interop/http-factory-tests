<?php

namespace Interop\Http\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use function strlen;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

abstract class UploadedFileFactoryTestCase extends TestCase
{
    /**
     * @var UploadedFileFactoryInterface
     */
    protected $factory;

    /**
     * @return UploadedFileFactoryInterface
     */
    abstract protected function createUploadedFileFactory();

    /**
     * @return StreamInterface
     */
    abstract protected function createStream($content);

    public function setUp(): void
    {
        $this->factory = $this->createUploadedFileFactory();
    }

    protected function assertUploadedFile(
        $file,
        $content,
        $size = null,
        $error = null,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        static::assertInstanceOf(UploadedFileInterface::class, $file);
        static::assertSame($content, (string) $file->getStream());
        static::assertSame(($size ?? strlen($content)), $file->getSize());
        static::assertSame(($error ?? UPLOAD_ERR_OK), $file->getError());
        static::assertSame($clientFilename, $file->getClientFilename());
        static::assertSame($clientMediaType, $file->getClientMediaType());
    }

    public function testCreateUploadedFileWithClientFilenameAndMediaType()
    {
        $content = 'this is your capitan speaking';
        $upload = $this->createStream($content);
        $error = UPLOAD_ERR_OK;
        $clientFilename = 'test.txt';
        $clientMediaType = 'text/plain';

        $file = $this->factory->createUploadedFile($upload, null, $error, $clientFilename, $clientMediaType);

        $this->assertUploadedFile($file, $content, null, $error, $clientFilename, $clientMediaType);
    }

    public function testCreateUploadedFileWithError()
    {
        $upload = $this->createStream('foobar');
        $error = UPLOAD_ERR_NO_FILE;

        $file = $this->factory->createUploadedFile($upload, null, $error);

        // Cannot use assertUploadedFile() here because the error prevents
        // fetching the content stream.
        static::assertInstanceOf(UploadedFileInterface::class, $file);
        static::assertSame($error, $file->getError());
    }
}
