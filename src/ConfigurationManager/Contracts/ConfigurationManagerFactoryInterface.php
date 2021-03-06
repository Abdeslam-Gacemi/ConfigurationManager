<?php

/**
* @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Abdeslam\ConfigurationManager\Contracts;

use Abdeslam\ConfigurationManager\Contracts\ConfigurationManagerInterface;

interface ConfigurationManagerFactoryInterface
{
    /**
     * Create a ConfigurationManager::class instance with the valid loader instance and loads the configuration files
     *
     * @param string ...$filePaths php configuration files to load
     * @return ConfigurationManagerInterface
     */
    public static function create(string $configurationFileType, string ...$filePaths): ConfigurationManagerInterface;
    
    /**
     * adds a configuration loader class name with its alias to the array of loaders
     *
     * @param string $alias
     * @param string $loaderClassName
     * @return void
     */
    public static function addConfigurationLoader(string $alias, string $loaderClassName): void;
}
