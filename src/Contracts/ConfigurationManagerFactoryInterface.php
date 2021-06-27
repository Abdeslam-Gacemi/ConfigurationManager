<?php

namespace Abdeslam\Configuration\Contracts;

use Abdeslam\Configuration\ConfigurationManager;

interface ConfigurationManagerFactoryInterface
{
    public static function createPHPConfigurationManager(string ...$filePaths): ConfigurationManager;
    public static function createJSONConfigurationManager(string ...$filePaths): ConfigurationManager;
}
