<?php

namespace Abdeslam\Configuration\Contracts;

use Abdeslam\Configuration\Exceptions\ConfigurationItemNotFoundException;
use Abdeslam\Configuration\Contracts\ConfigurationLoaderInterface;

interface ConfigurationManagerInterface
{
    /** @var string */
    const NO_DEFAULT_VALUE = '__no_default_value__';

    /**
     * @param ConfigurationLoaderInterface $loader a configuration loader
     * @param string|string[] $filePath the path of configuration file or files to load
     * @return self
    */
    public function addLoader(ConfigurationLoaderInterface $loader, $filePath): self;

    /**
     * return a loader from the registered loaders array
     *
     * @param string $loader loader class name
     * @return ConfigurationLoaderInterface|null
     */
    public function getLoader(string $loader): ?ConfigurationLoaderInterface;

    /**
     * returns the array of registered loaders
     *
     * @return array
     */
    public function getLoaders(): array;

    /**
     * checks if a configuration item exits by its key in the items array
     *
     * @param string $key
     * @return boolean
     */
    public function has($key): bool;

    /**
     * gets a configuration item from the items array
     *
     * @param string $key
     * @param mixed $default a default value to return in case of not finding an item by the key
     * @return mixed
     * @throws ConfigurationItemNotFoundException
     */
    public function get($key, $default = self::NO_DEFAULT_VALUE): mixed;

    /**
     * adds a configuration item to the items array
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function set(string $key, $value): self;

    /**
     * removes an item from the configuration items array
     *
     * @param string $key
     * @return self
     */
    public function unset(string $key): self;

    /**
     * return the array of configuration items
     *
     * @return array
     */
    public function all(): array;

    /**
     * merge the new items with old configuration items array
     *
     * @param array|self $items
     * @return self
     */
    public function merge($items): self;

    /**
     * sets the separator of compound keys
     * ex: 'key1.key2"
     *
     * @param string $separator
     * @return self
     */
    public function setKeySeparator(string $separator): self;

    /**
     * returns the key separator of compound keys
     *
     * @return string
     */
    public function getKeySeparator(): string;

    /**
     * resets the ConfigurationManager
     *
     * @return void
     */
    public function reset(): self;
}
