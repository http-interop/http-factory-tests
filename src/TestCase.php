<?php

namespace Http\FactoryTest;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function createTemporaryFile()
    {
        return tempnam(sys_get_temp_dir(), uniqid());
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
}
