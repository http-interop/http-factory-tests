<?php

namespace Interop\Http\Factory;

final class ServerRequestFactoryTest extends ServerRequestFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createServerRequestFactory()
    {
        $factoryClass = SERVER_REQUEST_FACTORY;

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
