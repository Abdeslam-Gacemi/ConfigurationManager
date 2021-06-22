<?php

namespace Abdeslam\Configuration\Contracts;

use Abdeslam\Configuration\ConfigurationManager;

interface ConfigurationManagerFactoryInterface
{
    public static function createPHPConfigurationManager($filePath): ConfigurationManager;
    public static function createJSONConfigurationManager($filePath): ConfigurationManager;
}
