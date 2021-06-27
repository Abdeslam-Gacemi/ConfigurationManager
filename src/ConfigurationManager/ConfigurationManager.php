<?php

/**
* @author Abdeslam Gacemi <abdobling@gmail.com>
*/

namespace Abdeslam\ConfigurationManager;

use Abdeslam\ConfigurationManager\Exceptions\InvalidKeyException;
use Abdeslam\ConfigurationManager\Exceptions\InvalidMergeItemsException;
use Abdeslam\ConfigurationManager\Contracts\ConfigurationLoaderInterface;
use Abdeslam\ConfigurationManager\Contracts\ConfigurationManagerInterface;
use Abdeslam\ConfigurationManager\Exceptions\ConfigurationItemNotFoundException;

class ConfigurationManager implements ConfigurationManagerInterface
{
    /** @var ConfigurationLoaderInterface[] */
    protected $loaders = [];

    /** @var array */
    protected $items = [];

    /** @var string */
    protected $keySeparator = '.';

    /** @var string[] */
    protected $loadedFiles = [];

    /** @var array */
    protected $files = [];

    /**
     * @inheritDoc
     */
    public function addLoader(ConfigurationLoaderInterface $loader, string ...$filepaths): ConfigurationManagerInterface
    {
        $loaderClassName = get_class($loader);
        $this->loaders[$loaderClassName] = $loader;
        if (!array_key_exists($loaderClassName, $this->files)) {
            $this->files[$loaderClassName] = [];
        }
        $this->files[$loaderClassName] = array_merge(
            $this->files[$loaderClassName],
            $filepaths
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLoader(string $loader): ?ConfigurationLoaderInterface
    {
        return $this->loaders[$loader] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function hasLoader(string $loader): bool
    {
        return isset($this->loaders[$loader]);
    }

    /**
     * @inheritDoc
     */
    public function getLoaders(): array
    {
        return $this->loaders;
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        $keys = $this->resolveKey($key);
        $current = $this->items;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $current)) {
                return false;
            }
            $current = $current[$key];
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = self::NO_DEFAULT_VALUE): mixed
    {
        $keys = $this->resolveKey($key);
        $current = $this->items;
        foreach ($keys as $key) {
            if (!isset($current[$key])) {
                if ($default !== self::NO_DEFAULT_VALUE) {
                    return $default;
                } else {
                    throw new ConfigurationItemNotFoundException("Item with the key '$key' not found in " . json_encode($current));
                }
            }
            $current = $current[$key];
        }
        return $current;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value): ConfigurationManagerInterface
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): ConfigurationManagerInterface
    {
        unset($this->items[$key]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function merge($items): ConfigurationManagerInterface
    {
        if (is_array($items)) {
            $this->items = array_merge($this->items, $items);
        } elseif ($items instanceof ConfigurationManager) {
            $this->items = array_merge($this->items, $items->all());
        } else {
            throw new InvalidMergeItemsException("Invalid items to merge, items must be an array or an instance of Abdeslam\ConfigurationManager\Configuration");
        }
        return $this;
    }

    public function getLoadedFiles(): array
    {
        return $this->loadedFiles;
    }

    /**
     * @inheritDoc
     */
    public function setKeySeparator(string $separator): ConfigurationManagerInterface
    {
        $this->keySeparator = $separator;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getKeySeparator(): string
    {
        return $this->keySeparator;
    }

    /**
     * @inheritDoc
     */
    public function reset(): ConfigurationManagerInterface
    {
        $this->loaders = [];
        $this->items = [];
        $this->loadedFiles = [];
        $this->files = [];
        $this->keySeparator = '.';
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return ConfigurationManagerInterface
     */
    public function load(): ConfigurationManagerInterface
    {
        foreach ($this->files as $loaderClassName => $files) {
            $loader = $this->loaders[$loaderClassName];
            $this->merge($loader->load($files));
            $this->loadedFiles = array_merge($this->loadedFiles, $files);
        }
        return $this;
    }

    /**
     * explodes a compound key to an array of keys and returns it
     *
     * @param string|array $key
     * @return array
     * @throws InvalidKeyException
     */
    protected function resolveKey($key): array
    {
        if (is_array($key)) {
            return $key;
        } elseif (is_string($key)) {
            return explode($this->keySeparator, $key);
        }
        $keyType = gettype($key);
        throw new InvalidKeyException("The key must be either a string or array, $keyType given");
    }
}
