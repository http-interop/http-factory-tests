<?php
/**
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use function class_exists;
use function defined;

final class ServerRequestFactoryTest extends ServerRequestFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createServerRequestFactory()
    {
        if (!defined('SERVER_REQUEST_FACTORY') || !class_exists(SERVER_REQUEST_FACTORY)) {
            self::markTestSkipped('SERVER_REQUEST_FACTORY class name not provided');
        }

        return new (SERVER_REQUEST_FACTORY);
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
