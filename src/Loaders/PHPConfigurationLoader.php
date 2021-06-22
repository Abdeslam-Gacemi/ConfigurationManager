<?php

namespace Abdeslam\Configuration\Loaders;

use Abdeslam\Configuration\Contracts\ConfigurationLoaderInterface;
use Abdeslam\Configuration\Exceptions\ConfigurationFileNotFoundException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationContentException;

class PHPConfigurationLoader implements ConfigurationLoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load($filePath): array
    {
        $resultContent = [];
        if (is_string($filePath)) {
            $resultContent = $this->loadFile($filePath);
        } elseif (is_array($filePath)) {
            foreach ($filePath as $path) {
                $content = $this->loadFile($path);
                $resultContent = array_merge($resultContent, $content);
            }
        }

        return $resultContent;
    }

    protected function loadFile(string $filePath): array
    {
        $this->checkFileExists($filePath);
        $content = require($filePath);
        if (!is_array($content)) {
            throw new InvalidConfigurationContentException("PHP configuration file '$filePath' must return an array");
        }
        return $content;
    }


    protected function checkFileExists(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new ConfigurationFileNotFoundException("File '$filePath' not found");
        }
    }
}
