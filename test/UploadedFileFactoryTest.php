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
}
