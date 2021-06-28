<?php

/**
 * @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Abdeslam\ConfigurationManager\Loaders;

use Abdeslam\Envator\EnvatorFactory;
use Abdeslam\ConfigurationManager\Contracts\ConfigurationLoaderInterface;

class ENVConfigurationLoader implements ConfigurationLoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(array $filepaths): array
    {
        $envator = EnvatorFactory::create($filepaths);
        return $envator->all();
    }
}