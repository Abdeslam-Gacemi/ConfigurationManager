<?php

namespace Abdeslam\Configuration\Contracts;

interface ConfigurationLoaderInterface
{

    /**
     * loads and return the content of a configuration file or files
     *
     * @param string|array $filePath
     * @return array
     */
    public function load(array $filePath): array;
}
