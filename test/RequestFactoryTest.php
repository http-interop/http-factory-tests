<?php

namespace Interop\Http\Factory;

final class RequestFactoryTest extends RequestFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createRequestFactory()
    {
        $factoryClass = REQUEST_FACTORY;

        return new $factoryClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function createUri($uri)
    {
        $factoryClass = URI_FACTORY;
        $uriFactory = new $factoryClass();

        return $uriFactory->createUri($uri);
    }
}
