<?php

namespace Interop\Http\Factory;

final class StreamFactoryTest extends StreamFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createStreamFactory()
    {
        $factoryClass = STREAM_FACTORY;

        return new $factoryClass();
    }
}
