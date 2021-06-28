<?php

/**
* @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Abdeslam\ConfigurationManager;

use ReflectionClass;
use Abdeslam\ConfigurationManager\ConfigurationManager;
use Abdeslam\ConfigurationManager\Loaders\PHPConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\XMLConfigurationLoader;
use Abdeslam\ConfigurationManager\Loaders\JSONConfigurationLoader;
use Abdeslam\ConfigurationManager\Contracts\ConfigurationManagerFactoryInterface;
use Abdeslam\ConfigurationManager\Contracts\ConfigurationManagerInterface;
use Abdeslam\ConfigurationManager\Exceptions\InvalidConfigurationLoaderException;
use Abdeslam\ConfigurationManager\Loaders\ENVConfigurationLoader;

class ConfigurationManagerFactory implements ConfigurationManagerFactoryInterface
{
    protected static $supportedConfigurationLoaders = [
        'php' => PHPConfigurationLoader::class,
        'json' => JSONConfigurationLoader::class,
        'xml' => XMLConfigurationLoader::class,
        'env' => ENVConfigurationLoader::class
    ];

    /**
     * @inheritDoc
     */
    public static function create(string $alias, string ...$filepaths): ConfigurationManagerInterface
    {
        if (array_key_exists($alias, self::$supportedConfigurationLoaders)) {
            $loader = new self::$supportedConfigurationLoaders[$alias]();
            $manager = new ConfigurationManager();
            $manager->addLoader($loader, ...$filepaths);
            return $manager;
        }
    }

    /**
     * @inheritDoc
     */
    public static function addConfigurationLoader(string $alias, string $loaderClassName): void
    {
        try {
            $reflect = new ReflectionClass($loaderClassName);
        } catch (\ReflectionException $e) {
            throw new InvalidConfigurationLoaderException($e->getMessage());
        }
        if ($reflect->isInstantiable()) {
            self::$supportedConfigurationLoaders[$alias] = $loaderClassName;
        } else {
            throw new InvalidConfigurationLoaderException("Error statement");
        }
    }
}
