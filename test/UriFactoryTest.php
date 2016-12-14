<?php

namespace Interop\Http\Factory;

final class UriFactoryTest extends UriFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createUriFactory()
    {
        if (!defined('URI_FACTORY')) {
            $this->markTestSkipped('URI factory class name not provided');
        }

        $factoryClass = URI_FACTORY;

        return new $factoryClass();
    }
}
