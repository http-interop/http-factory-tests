<?php

namespace Interop\Http\Factory;

final class UploadedFileFactoryTest extends UploadedFileFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createUploadedFileFactory()
    {
        $factoryClass = UPLOADED_FILE_FACTORY;

        return new $factoryClass();
    }
}
