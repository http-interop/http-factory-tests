<?php
/**
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use function class_exists;
use function defined;

final class StreamFactoryTest extends StreamFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createStreamFactory()
    {
        if (!defined('STREAM_FACTORY') || !class_exists(STREAM_FACTORY)) {
            self::markTestSkipped('Stream factory class name not provided');
        }

        return new (STREAM_FACTORY);
    }
}
