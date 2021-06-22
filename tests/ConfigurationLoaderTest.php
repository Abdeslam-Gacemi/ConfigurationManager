<?php

namespace Tests;

use Abdeslam\Configuration\Loaders\PHPConfigurationLoader;
use PHPUnit\Framework\TestCase;

class ConfigurationLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function loaderInit()
    {
        $loader = new PHPConfigurationLoader();
        $configArray = $loader->load([
            __DIR__ . '/config/config.php',
            __DIR__ . '/config/config2.php',
        ]);
        $this->assertIsArray($configArray);
        $this->assertSame('my-app', $configArray['app']);
        $this->assertSame('user1', $configArray['credentials']['username']);
    }
}
