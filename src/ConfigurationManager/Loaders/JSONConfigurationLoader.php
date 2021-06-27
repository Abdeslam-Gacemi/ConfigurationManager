<?php

/**
* @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Abdeslam\ConfigurationManager\Loaders;

use Abdeslam\ConfigurationManager\Contracts\ConfigurationLoaderInterface;
use Abdeslam\ConfigurationManager\Exceptions\InvalidConfigurationFileException;
use Abdeslam\ConfigurationManager\Exceptions\ConfigurationFileNotFoundException;
use Abdeslam\ConfigurationManager\Exceptions\InvalidConfigurationContentException;

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

    /**
     * loads a single configuration file
     *
     * @param string $filepath
     * @return array
     * @throws InvalidConfigurationContentException
     */
    protected function loadFile(string $filepath): array
    {
        $this->validateFile($filepath);

        $content = json_decode(file_get_contents($filepath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidConfigurationContentException("Invalid json content from file $filepath");
        }

        return $content;
    }

    /**
     * checks if the configuration file is valid
     *
     * @param string $filepath
     * @return void
     * @throws ConfigurationFileNotFoundException
     * @throws InvalidConfigurationFileException
     */
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
