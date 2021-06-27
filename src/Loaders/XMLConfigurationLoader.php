<?php

namespace Abdeslam\Configuration\Loaders;

use Abdeslam\Configuration\Contracts\ConfigurationLoaderInterface;
use Abdeslam\Configuration\Exceptions\ConfigurationFileNotFoundException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationContentException;
use Abdeslam\Configuration\Exceptions\InvalidConfigurationFileException;

class XMLConfigurationLoader implements ConfigurationLoaderInterface
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
        libxml_use_internal_errors(true);
        $objXmlDocument = simplexml_load_file($filepath, "SimpleXMLElement", LIBXML_COMPACT | LIBXML_DTDATTR);
        if ($objXmlDocument === false) {
            throw new InvalidConfigurationFileException(implode(', ', libxml_get_errors()));
        }
        $jsonContent = json_encode($objXmlDocument);
        $content = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidConfigurationContentException(json_last_error_msg());
        }
        array_walk_recursive($content, function (&$value, $key) {
            if (strtolower($value) === 'true' || strtolower($value) === 'false') {
                $value = strtolower($value) === 'true' ? true : false;
            }
            if (is_numeric($value)) {
                $value = strpos($value, '.') !== false ? (float) $value : (int) $value;
            }
        });
        return $content;
    }


    protected function validateFile(string $filepath): void
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        if (!file_exists($filepath)) {
            throw new ConfigurationFileNotFoundException("File '$filepath' not found");
        }
        if ($ext !== 'xml') {
            throw new InvalidConfigurationFileException("Configuration File extension must be .xml");
        }
    }
}
