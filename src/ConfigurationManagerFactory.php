<?php

namespace Abdeslam\Configuration;

use Abdeslam\Configuration\ConfigurationManager;
use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;
use Abdeslam\Configuration\Loaders\JSONConfigurationLoader;
use Abdeslam\Configuration\Contracts\ConfigurationManagerFactoryInterface;

class ConfigurationManagerFactory implements ConfigurationManagerFactoryInterface
{
    public static function createPHPConfigurationManager($filePath): ConfigurationManager
    {
        $loader = new PHPConfigurationLoader();
        $config =  new ConfigurationManager();
        return $config->addLoader($loader, $filePath);
    }

    public static function createJSONConfigurationManager($filePath): ConfigurationManager
    {
        $loader = new JSONConfigurationLoader();
        $config =  new ConfigurationManager();
        return $config->addLoader($loader, $filePath);
    }
}
