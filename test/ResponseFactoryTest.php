<?php
/**
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use function class_exists;
use function defined;

final class ResponseFactoryTest extends ResponseFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createResponseFactory()
    {
        if (!defined('RESPONSE_FACTORY') || !class_exists(RESPONSE_FACTORY)) {
            self::markTestSkipped('RESPONSE_FACTORY class name not provided');
        }

        return new (RESPONSE_FACTORY);
    }
}
