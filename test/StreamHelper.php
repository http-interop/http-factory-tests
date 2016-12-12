<?php

namespace Interop\Http\Factory;

trait StreamHelper
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
