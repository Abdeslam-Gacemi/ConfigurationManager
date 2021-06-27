<?php

namespace Abdeslam\Configuration\Loaders;

use Abdeslam\Configuration\Contracts\ConfigurationLoaderInterface;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationFileException;
use Abdeslam\Configuration\Exceptions\ConfigurationFileNotFoundException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationContentException;

class JSONConfigurationLoader implements ConfigurationLoaderInterface
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

        $content = json_decode(file_get_contents($filepath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidConfigurationContentException("Invalid json content from file $filepath");
        }

        return $content;
    }


    protected function validateFile(string $filepath): void
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        if (!file_exists($filepath)) {
            throw new ConfigurationFileNotFoundException("File '$filepath' not found");
        }
        if ($ext !== 'json') {
            throw new InvalidConfigurationFileException("Configuration File extension must be .json");
        }
    }
}
