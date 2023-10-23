<?php
/**
 * @noinspection PhpUndefinedConstantInspection
 */

namespace Interop\Http\Factory;

use function class_exists;
use function defined;

final class UriFactoryTest extends UriFactoryTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createUriFactory()
    {
        if (!defined('URI_FACTORY') || !class_exists(URI_FACTORY)) {
            self::markTestSkipped('URI_FACTORY class name not provided');
        }

        return new (URI_FACTORY);
    }
}
