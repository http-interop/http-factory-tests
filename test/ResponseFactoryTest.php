<?php

namespace Interop\Http\Factory;

final class ResponseFactoryTest extends ResponseFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createResponseFactory()
    {
        if (!defined('RESPONSE_FACTORY')) {
            $this->markTestSkipped('Response factory class name not provided');
        }

        $factoryClass = RESPONSE_FACTORY;

        return new $factoryClass();
    }
}
