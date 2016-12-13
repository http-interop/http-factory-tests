<?php

namespace Interop\Http\Factory;

final class ResponseFactoryTest extends ResponseFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createResponseFactory()
    {
        $factoryClass = RESPONSE_FACTORY;

        return new $factoryClass();
    }
}
