<?php

namespace Abdeslam\Configuration;

use Abdeslam\Configuration\ConfigurationManager;
use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;
use Abdeslam\Configuration\Loaders\JSONConfigurationLoader;
use Abdeslam\Configuration\Contracts\ConfigurationManagerFactoryInterface;

class ConfigurationManagerFactory implements ConfigurationManagerFactoryInterface
{
    public static function createPHPConfigurationManager(string ...$filePaths): ConfigurationManager
    {
        $loader = new PHPConfigurationLoader();
        $config =  new ConfigurationManager();
        return $config->addLoader($loader, ...$filePaths);
    }

    public static function createJSONConfigurationManager(string ...$filePaths): ConfigurationManager
    {
        $loader = new JSONConfigurationLoader();
        $config =  new ConfigurationManager();
        return $config->addLoader($loader, ...$filePaths);
    }
}
