<?php

namespace Abdeslam\Configuration\Loaders;

use Abdeslam\Configuration\Contracts\ConfigurationLoaderInterface;
use Abdeslam\Configuration\Exceptions\ConfigurationFileNotFoundException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationContentException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationFileException;

class PHPConfigurationLoader implements ConfigurationLoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(array $filepaths): array
    {
        $resultContent = [];
        foreach ($filepaths as $filepath) {
            $content = $this->loadFile($filepath);
            $resultContent = array_merge($resultContent, $content);
        }
        return $resultContent;
    }

    protected function loadFile(string $filepath): array
    {
        $this->validateFile($filepath);
        $content = require($filepath);
        if (!is_array($content)) {
            throw new InvalidConfigurationContentException("PHP configuration file '$filepath' must return an array");
        }
        return $content;
    }


    protected function validateFile(string $filepath): void
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        if (!file_exists($filepath)) {
            throw new ConfigurationFileNotFoundException("File '$filepath' not found");
        }
        if ($ext !== 'php') {
            throw new InvalidConfigurationFileException("Configuration File extension must be .php");
        }
    }
}
