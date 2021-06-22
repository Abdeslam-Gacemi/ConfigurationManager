<?php

namespace Abdeslam\Configuration;

use Abdeslam\Configuration\Contracts\ConfigurationLoaderInterface;
use Abdeslam\Configuration\Contracts\ConfigurationManagerInterface;
use Abdeslam\Configuration\Exceptions\ConfigurationItemNotFoundException;
use Abdeslam\Configuration\Exceptions\InvalidKeyException;
use Abdeslam\Configuration\Exceptions\InvalidMergeItemsException;

class ConfigurationManager implements ConfigurationManagerInterface
{
    /** @var ConfigurationLoaderInterface[] */
    protected $loaders;

    /** @var array */
    protected $items = [];

    /** @var string */
    protected $keySeparator = '.';

    /**
     * @inheritDoc
     */
    public function addLoader(ConfigurationLoaderInterface $loader, $filename): ConfigurationManagerInterface
    {
        $key = get_class($loader);
        $this->loaders[$key] = $loader;
        $this->filename = $filename;
        $this->merge($loader->load($filename));
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
            if (!isset($current[$key])) {
                if (!isset($current[$key])) {
                    return false;
                }
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
                    throw new ConfigurationItemNotFoundException("Item with the key '$key' not found");
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
            throw new InvalidMergeItemsException("Invalid items to merge, items must be an array or an instance of Abdeslam\Configuration\Configuration");
        }
        return $this;
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
        $this->keySeparator = '.';
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
