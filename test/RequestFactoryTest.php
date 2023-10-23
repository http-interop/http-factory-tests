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
            self::markTestSkipped('REQUEST_FACTORY class name not provided');
        }

        return new (REQUEST_FACTORY);
    }

    /**
     * {@inheritdoc}
     */
    protected function createUri($uri)
    {
        if (!defined('URI_FACTORY') || !class_exists(URI_FACTORY)) {
            self::markTestSkipped('URI_FACTORY class name not provided');
        }

        return (new (URI_FACTORY))->createUri($uri);
    }
}
