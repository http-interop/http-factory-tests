<?php

namespace Interop\Http\Factory;

use RuntimeException;
use function fopen;
use function fwrite;
use function is_file;
use function rewind;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

trait StreamHelper
{
    protected static $tempFiles = [];

    protected function createTemporaryFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'http_factory_tests_');

        if($file === false){
            throw new RuntimeException('could not create temp file');
        }

        static::$tempFiles[] = $file;

        return $file;
    }

    protected function createTemporaryResource($content = null)
    {
        $file = $this->createTemporaryFile();
        $resource = fopen($file, 'r+');

        if ($content) {
            fwrite($resource, $content);
            rewind($resource);
        }

        return $resource;
    }

    public static function tearDownAfterClass(): void
    {
        foreach (static::$tempFiles as $tempFile) {
            if (is_file($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}
