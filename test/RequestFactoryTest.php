<?php
/**
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use function class_exists;
use function defined;

final class RequestFactoryTest extends RequestFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createRequestFactory()
    {
        if (!defined('REQUEST_FACTORY') || !class_exists(REQUEST_FACTORY)) {
            self::markTestSkipped('Request factory class name not provided');
        }

        return new (REQUEST_FACTORY);
    }

    /**
     * {@inheritdoc}
     */
    protected function createUri($uri)
    {
        if (!defined('URI_FACTORY')) {
            self::markTestSkipped('URI factory class name not provided');
        }

        return (new (URI_FACTORY))->createUri($uri);
    }
}
