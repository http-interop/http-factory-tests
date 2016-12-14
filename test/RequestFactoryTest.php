<?php

namespace Interop\Http\Factory;

final class RequestFactoryTest extends RequestFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createRequestFactory()
    {
        if (!defined('REQUEST_FACTORY')) {
            $this->markTestSkipped('Request factory class name not provided');
        }

        $factoryClass = REQUEST_FACTORY;

        return new $factoryClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function createUri($uri)
    {
        if (!defined('URI_FACTORY')) {
            $this->markTestSkipped('URI factory class name not provided');
        }

        $factoryClass = URI_FACTORY;
        $uriFactory = new $factoryClass();

        return $uriFactory->createUri($uri);
    }
}
