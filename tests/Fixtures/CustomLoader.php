<?php

/**
 * @author Abdeslam Gacemi <abdobling@gmail.com>
 */

namespace Tests\Fixtures;

use Abdeslam\ConfigurationManager\Contracts\ConfigurationLoaderInterface;

class CustomLoader implements ConfigurationLoaderInterface
{
    /**
     * @inheritDoc
     */
    public function load(array $filePath): array
    {
        return [
            'hello' => 'world'
        ];
    }
}