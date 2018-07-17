<?php

namespace Interop\Http\Factory;

final class UploadedFileFactoryTest extends UploadedFileFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createUploadedFileFactory()
    {
        if (!defined('UPLOADED_FILE_FACTORY')) {
            $this->markTestSkipped('Uploaded File factory class name not provided');
        }

        $factoryClass = UPLOADED_FILE_FACTORY;

        return new $factoryClass();
    }

    protected function createStream($content)
    {
        if (!defined('STREAM_FACTORY')) {
            $this->markTestSkipped('STREAM factory class name not provided');
        }

        $factoryClass = STREAM_FACTORY;
        $uriFactory = new $factoryClass();

        return $uriFactory->createStream($content);
    }
}
