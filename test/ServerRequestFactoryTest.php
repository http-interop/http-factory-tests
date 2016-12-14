<?php

namespace Interop\Http\Factory;

final class ServerRequestFactoryTest extends ServerRequestFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createServerRequestFactory()
    {
        if (!defined('SERVER_REQUEST_FACTORY')) {
            $this->markTestSkipped('Server Request factory class name not provided');
        }

        $factoryClass = SERVER_REQUEST_FACTORY;

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
