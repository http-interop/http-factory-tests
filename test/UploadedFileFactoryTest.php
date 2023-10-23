<?php
/**
 * @author       http-factory-tests Contributors
 * @license      MIT
 * @link         https://github.com/http-interop/http-factory-tests
 *
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use function class_exists;
use function defined;
use function strlen;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

class UploadedFileFactoryTest extends TestCase
{

    protected UploadedFileFactoryInterface $uploadedFileFactory;
    protected StreamFactoryInterface       $streamFactory;

    public function setUp(): void
    {
        $this->uploadedFileFactory = $this->createUploadedFileFactory();
        $this->streamFactory       = $this->createStreamFactory();
    }

    protected function createUploadedFileFactory(): UploadedFileFactoryInterface
    {
        if(!defined('UPLOADED_FILE_FACTORY') || !class_exists(UPLOADED_FILE_FACTORY)){
            static::markTestSkipped('UPLOADED_FILE_FACTORY class name not provided');
        }

        return new (UPLOADED_FILE_FACTORY);
    }

    protected function createStreamFactory(): StreamFactoryInterface
    {
        if(!defined('STREAM_FACTORY') || !class_exists(STREAM_FACTORY)){
            static::markTestSkipped('STREAM_FACTORY class name not provided');
        }

        return new (STREAM_FACTORY);
    }

    public function testCreateUploadedFileWithClientFilenameAndMediaType(): void
    {
        $content         = 'this is your capitan speaking';
        $upload          = $this->streamFactory->createStream($content);
        $error           = UPLOAD_ERR_OK;
        $clientFilename  = 'test.txt';
        $clientMediaType = 'text/plain';

        $file = $this->uploadedFileFactory->createUploadedFile($upload, null, $error, $clientFilename, $clientMediaType);

        static::assertSame($content, (string) $file->getStream());
        static::assertSame(strlen($content), $file->getSize());
        static::assertSame($error, $file->getError());
        static::assertSame($clientFilename, $file->getClientFilename());
        static::assertSame($clientMediaType, $file->getClientMediaType());
    }

    public function testCreateUploadedFileWithError(): void
    {
        $upload = $this->streamFactory->createStream('foobar');
        $error  = UPLOAD_ERR_NO_FILE;
        $file   = $this->uploadedFileFactory->createUploadedFile($upload, null, $error);

        static::assertSame($error, $file->getError());
    }

}
