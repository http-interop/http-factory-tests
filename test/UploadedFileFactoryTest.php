<?php
/**
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use function class_exists;
use function defined;

final class UploadedFileFactoryTest extends UploadedFileFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createUploadedFileFactory()
    {
        if (!defined('UPLOADED_FILE_FACTORY') || !class_exists(UPLOADED_FILE_FACTORY)) {
            self::markTestSkipped('UPLOADED_FILE_FACTORY class name not provided');
        }

        return new (UPLOADED_FILE_FACTORY);
    }

    protected function createStream($content)
    {
        if (!defined('STREAM_FACTORY') || !class_exists(STREAM_FACTORY)) {
            self::markTestSkipped('STREAM_FACTORY class name not provided');
        }

        return (new (STREAM_FACTORY))->createStream($content);
    }
}
