<?php

namespace Interop\Http\Factory;

final class StreamFactoryTest extends StreamFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createStreamFactory()
    {
        if (!defined('STREAM_FACTORY')) {
            $this->markTestSkipped('Stream factory class name not provided');
        }

        $factoryClass = STREAM_FACTORY;

        return new $factoryClass();
    }
}
